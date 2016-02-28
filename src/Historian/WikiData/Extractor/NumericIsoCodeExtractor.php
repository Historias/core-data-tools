<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class NumericIsoCodeExtractor extends StatementValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[numeric_iso_code]');
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
