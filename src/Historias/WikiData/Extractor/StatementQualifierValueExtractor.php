<?php
namespace Historias\Importer\WikiData\Extractor;

use Historias\Importer\WikiData\Claim;
use Historias\Importer\WikiData\Property;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\StatementList;

abstract class StatementQualifierValueExtractor extends StatementValueExtractor
{
    abstract protected function getClaim() : Claim;

    abstract protected function getQualifierProperty() : Property;

    protected function getStatementValue(StatementList $statements, Property $property)
    {
        $statements = $statements->getByPropertyId(PropertyId::newFromNumber($property->value()));

        if ($statements->isEmpty()) {
            return null;
        }

        foreach ($statements as $statement) {
            if ($statement->getMainSnak()->getDataValue()->getValue()['numeric-id'] !== $this->getClaim()->value()) {
                continue;
            }

            /** @var PropertyValueSnak $snak */
            foreach ($statement->getQualifiers() as $snak) {
                if ($snak->getPropertyId()->getNumericId() === $this->getQualifierProperty()->value()) {
                    return $snak->getDataValue();
                }
            }
        }

        return null;
    }
}
