<?php
namespace Historias\Importer;

use Historias\Importer\WikiData\Extractor\Extractor;
use Ramsey\Uuid\Uuid;
use Throwable;

interface ErrorLogger
{
    public function logExtractorError(Uuid $uuid, string $nativeId, Extractor $extractor, Throwable $e);
}
