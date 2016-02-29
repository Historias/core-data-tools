<?php
namespace Historian\Importer\WikiData;

use Historian\Importer\ErrorLogger;
use Historian\Importer\Filter;
use Historian\Importer\Identity\UuidMap;
use Historian\Importer\Importer;
use Historian\Importer\ProgressLogger;
use Historian\Importer\WikiData\Extractor\Extractor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Throwable;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Services\Lookup\ItemLookup;

class WikiDataImporter implements Importer
{
    private $idMap;

    private $itemFinder;

    private $itemLookup;

    private $extractors;

    private $propertyAccessor;

    /**
     * @param UuidMap $idMap
     * @param ItemFinder $itemFinder
     * @param ItemLookup $itemLookup
     * @param Extractor[] $extractors
     */
    public function __construct(UuidMap $idMap, ItemFinder $itemFinder, ItemLookup $itemLookup, array $extractors)
    {
        $this->idMap = $idMap;
        $this->itemFinder = $itemFinder;
        $this->itemLookup = $itemLookup;
        $this->extractors = $extractors;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function import(Filter $filter, ProgressLogger $progressLogger, ErrorLogger $errorLogger) : array
    {
        $this->itemFinder->find(Property::INSTANCE_OF(), Claim::FORMER_COUNTRY());
        $this->itemFinder->find(Property::INSTANCE_OF(), Claim::SOUVEREIGN_STATE());

        return $this->extractItems($filter, $progressLogger, $errorLogger);
    }

    private function extractItems(Filter $filter, ProgressLogger $progressLogger, ErrorLogger $errorLogger)
    {
        $data = [];

        foreach ($this->itemFinder->getMatchingItems() as $id) {
            if (!$filter->matches($id)) {
                continue;
            }

            $item = $this->extractItem($id, $progressLogger, $errorLogger);
            if ($item === null) {
                continue;
            }
            $data[] = $item;
        }

        return $data;
    }

    private function extractItem(string $id, ProgressLogger $progressLogger, ErrorLogger $errorLogger)
    {
        $uuid = $this->idMap->get($id);
        $country = ['uuid' => $uuid];

        $progressLogger->logImportBegin($uuid, $id);
        $item = $this->itemLookup->getItemForId(ItemId::newFromNumber($id));
        foreach ($this->extractors as $extractor) {
            $progressLogger->logExtractorProgress($uuid, $id, $extractor);
            try {
                $country = array_replace_recursive($country, $this->executeExtractor($extractor, $item));
            } catch (Throwable $e) {
                $errorLogger->logExtractorError($uuid, $id, $extractor, $e);
                continue;
            }
        }

        if (empty($country['name'])) {
            return null;
        }

        $progressLogger->logImportEnd($uuid, $id, $country);

        return $country;
    }

    private function executeExtractor(Extractor $extractor, Item $item)
    {
        $country = [];

        $value = $extractor->getValue($this->itemLookup, $item);
        if ($value !== null) {
            $this->propertyAccessor->setValue($country, $extractor->getPath(), $value);
        }

        return $country;
    }
}
