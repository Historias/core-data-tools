<?php
namespace Historias\Importer\WikiData\Extractor;

use Historias\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class ExtractEndOfSpanFromDissolution extends StatementValueExtractor
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
