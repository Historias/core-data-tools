<?php
namespace Historian\Importer;

use Historian\Importer\WikiData\Extractor\Extractor;
use Ramsey\Uuid\Uuid;
use Throwable;

interface ErrorLogger
{
    public function logExtractorError(Uuid $uuid, string $nativeId, Extractor $extractor, Throwable $e);
}
