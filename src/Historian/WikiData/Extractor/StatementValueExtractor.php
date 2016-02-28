<?php
namespace Historian\Importer\WikiData\Extractor;

use Historian\Importer\WikiData\Property;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\ItemLookup;
use Wikibase\DataModel\Statement\StatementList;

abstract class StatementValueExtractor implements Extractor
{
    private $recursionMap = [];

    final public function getValue(ItemLookup $itemLookup, Item $item)
    {
        $value = $this->getBestStatementValue($item, $this->getProperty());

        if ($value !== null) {
            return $this->formatValue($value);
        }

        if (!$this->getFallbackProperty()) {
            return null;
        }

        $fallbackId = $this->getBestStatementValue($item, $this->getFallbackProperty());
        if (!is_array($fallbackId)) {
            return null;
        }

        $nextItem = $itemLookup->getItemForId(ItemId::newFromNumber($fallbackId['numeric-id']));
        if ($this->hasRecursion($item, $nextItem)) {
            return null;
        }

        return $this->getValue($itemLookup, $nextItem);
    }

    abstract protected function getProperty() : Property;

    /** @return Property|null */
    protected function getFallbackProperty()
    {
        return null;
    }

    protected function formatValue($value)
    {
        return $value;
    }

    private function getBestStatementValue(Item $item, Property $property)
    {
        return $this->getStatementValue($item->getStatements()->getBestStatements(), $property)
            ?: $this->getStatementValue($item->getStatements(), $property);
    }

    private function getStatementValue(StatementList $statements, Property $property)
    {
        $statements = $statements->getByPropertyId(PropertyId::newFromNumber($property->value()));

        return $statements->isEmpty() ? null : $statements->getMainSnaks()[0]->getDataValue()->getValue();
    }

    private function hasRecursion(Item $currentItem, Item $nextItem) : bool
    {
        $itemId = $currentItem->getId()->getNumericId();
        $nextItemId = $nextItem->getId()->getNumericId();

        $this->recursionMap[$itemId] = $this->recursionMap[$itemId] ?? [];

        if (in_array($nextItemId, $this->recursionMap[$itemId], true)) {
            return true;
        }
        $this->recursionMap[$itemId][] = $nextItemId;

        return false;
    }
}
