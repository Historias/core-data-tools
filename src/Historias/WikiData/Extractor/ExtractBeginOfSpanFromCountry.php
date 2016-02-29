<?php
namespace Historias\Importer\WikiData\Extractor;

use Historias\Importer\WikiData\Claim;
use Historias\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class ExtractBeginOfSpanFromCountry extends StatementQualifierValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[span][begin]');
    }

    protected function getProperty() : Property
    {
        return Property::INSTANCE_OF();
    }

    protected function getClaim() : Claim
    {
        return Claim::COUNTRY();
    }

    protected function getQualifierProperty() : Property
    {
        return Property::START_TIME();
    }
}
