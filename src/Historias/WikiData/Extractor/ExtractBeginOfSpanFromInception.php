<?php
namespace Historias\Importer\WikiData\Extractor;

use Historias\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class ExtractBeginOfSpanFromInception extends StatementValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[span][begin]');
    }

    protected function getProperty() : Property
    {
        return Property::INCEPTION();
    }
}
