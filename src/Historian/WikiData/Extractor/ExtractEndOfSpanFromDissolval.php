<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class ExtractEndOfSpanFromDissolval extends StatementValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[span][end]');
    }

    protected function getProperty() : Property
    {
        return Property::DISSOLVED_OR_ABOLISHED();
    }
}
