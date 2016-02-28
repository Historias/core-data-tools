<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class IsoNumericCodeExtractor extends StatementValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[iso_numeric_code]');
    }

    protected function getProperty() : Property
    {
        return Property::ISO_NUMERIC_CODE();
    }

    protected function getFallbackProperty()
    {
        return Property::FOLLOWED_BY();
    }
}
