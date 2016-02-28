<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class DurationEndExtractor extends StatementDateValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[duration][end]');
    }

    protected function getProperty() : Property
    {
        return Property::DISSOLVED_OR_ABOLISHED();
    }
}
