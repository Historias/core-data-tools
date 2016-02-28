<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class Iso3LetterCodeExtractor extends StatementValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[iso_3_letter_code]');
    }

    protected function getProperty() : Property
    {
        return Property::ISO_3_LETTER_CODE();
    }

    protected function getFallbackProperty()
    {
        return Property::FOLLOWED_BY();
    }
}
