<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class ExtractThreeLetterIsoCode extends StatementValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[three_letter_iso_code]');
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
