<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class DurationBeginExtractor extends StatementDateValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[duration][begin]');
    }

    protected function getProperty() : Property
    {
        return Property::INCEPTION();
    }
}
