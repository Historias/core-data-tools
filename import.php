<?php
use DataValues\Deserializers\DataValueDeserializer;
use DataValues\Serializers\DataValueSerializer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Historian\Importer\ErrorLogger;
use Historian\Importer\Filter;
use Historian\Importer\Identity\UuidMap;
use Historian\Importer\LayerEditor;
use Historian\Importer\ProgressLogger;
use Historian\Importer\WikiData\Extractor\Extractor;
use Historian\Importer\WikiData\Extractor\NameExtractor;
use Historian\Importer\WikiData\Extractor\NumericIsoCodeExtractor;
use Historian\Importer\WikiData\Extractor\SpanBeginExtractor;
use Historian\Importer\WikiData\Extractor\SpanEndExtractor;
use Historian\Importer\WikiData\Extractor\ThreeLetterIsoCodeExtractor;
use Historian\Importer\WikiData\Extractor\TwoLetterIsoCodeExtractor;
use Historian\Importer\WikiData\Extractor\UrlExtractor;
use Historian\Importer\WikiData\ItemFinder;
use Historian\Importer\WikiData\WikiDataImporter;
use JsonSchema\Validator;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\FlysystemStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use League\Flysystem\Adapter\Local;
use Mediawiki\Api\MediawikiApi;
use Ramsey\Uuid\Uuid;
use Wikibase\Api\WikibaseFactory;

require __DIR__ . '/vendor/autoload.php';

$handler = HandlerStack::create();
$handler->push(
    new CacheMiddleware(
        new GreedyCacheStrategy(new FlysystemStorage(new Local(__DIR__ . '/cache')), 60 * 60 * 60)
    )
);

$filter = new class implements Filter {

    public function matches(string $nativeId) : bool
    {
        return isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] === $nativeId : true;
    }
};

$progressLogger = new class implements ProgressLogger {

    public function logImportBegin(Uuid $uuid, string $nativeId)
    {
        printf("Importing https://www.wikidata.org/wiki/Q%d as %s\n", $nativeId, $uuid);
    }

    public function logExtractorProgress(Uuid $uuid, string $nativeId, Extractor $extractor)
    {
        //printf("Running %s on https://www.wikidata.org/wiki/Q%d as %s\n", get_class($extractor), $nativeId, $uuid);
    }

    public function logImportEnd(Uuid $uuid, string $nativeId, array $data)
    {
        printf("Imported https://www.wikidata.org/wiki/Q%d as %s: %s\n", $nativeId, $uuid, json_encode($data));
    }
};
$errorLogger = new class implements ErrorLogger {

    public function logExtractorError(Uuid $uuid, string $nativeId, Extractor $extractor, Throwable $e)
    {
        printf(
            "Extraction of https://www.wikidata.org/wiki/Q%d failed in %s: %s\n",
            $nativeId,
            get_class($extractor),
            $e->getMessage()
        );
    }
};

$layerEditor = new LayerEditor(
    __DIR__ . '/data/wikidata/all.json',
    new WikiDataImporter(
        new UuidMap(__DIR__ . '/data/wikidata/identifiers.json'),
        new ItemFinder(
            new Client(['base_url' => 'https://wdq.wmflabs.org/api', 'handler' => $handler])
        ),
        (new WikibaseFactory(
            new MediawikiApi('https://www.wikidata.org/w/api.php', new Client(['handler' => $handler])),
            new DataValueDeserializer(),
            new DataValueSerializer()
        ))->newItemLookup(),
        [
            new NameExtractor(),
            new ThreeLetterIsoCodeExtractor(),
            new TwoLetterIsoCodeExtractor(),
            new NumericIsoCodeExtractor(),
            new SpanBeginExtractor(),
            new SpanEndExtractor(),
            new UrlExtractor(),
        ]
    ),
    new Validator(),
    'file:///' . __DIR__ . '/data/wikidata/schema.json'
);
$layerEditor->edit($filter, $progressLogger, $errorLogger);
