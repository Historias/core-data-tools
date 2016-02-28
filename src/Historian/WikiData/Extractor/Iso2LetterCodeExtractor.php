<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class Iso2LetterCodeExtractor extends StatementValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[iso_2_letter_code]');
    }

    protected function getProperty() : Property
    {
        return Property::ISO_2_LETTER_CODE();
    }

    protected function getFallbackProperty()
    {
        return Property::FOLLOWED_BY();
    }
}
