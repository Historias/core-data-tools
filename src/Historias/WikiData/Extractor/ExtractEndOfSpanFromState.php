<?php
namespace Historias\Importer\WikiData\Extractor;

use Historias\Importer\WikiData\Claim;
use Historias\Importer\WikiData\Property;
use Symfony\Component\PropertyAccess\PropertyPath;

class ExtractEndOfSpanFromState extends StatementQualifierValueExtractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[span][end]');
    }

    protected function getProperty() : Property
    {
        return Property::INSTANCE_OF();
    }

    protected function getClaim() : Claim
    {
        return Claim::STATE();
    }

    protected function getQualifierProperty() : Property
    {
        return Property::END_TIME();
    }
}
