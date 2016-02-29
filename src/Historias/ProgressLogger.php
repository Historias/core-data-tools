<?php
namespace Historias\Importer;

use Historias\Importer\WikiData\Extractor\Extractor;
use Ramsey\Uuid\Uuid;

interface ProgressLogger
{
    public function logImportBegin(Uuid $uuid, string $nativeId);

    public function logExtractorProgress(Uuid $uuid, string $nativeId, Extractor $extractor);

    public function logImportEnd(Uuid $uuid, string $nativeId, array $data);
}
