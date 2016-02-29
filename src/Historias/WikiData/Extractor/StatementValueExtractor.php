<?php
namespace Historias\Importer\WikiData\Extractor;

use DataValues\DataValue;
use DateTimeImmutable;
use Historias\Importer\Util\JsonUtil;
use Historias\Importer\WikiData\Property;
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

        $fallback = $this->getBestStatementValue($item, $this->getFallbackProperty());
        if ($fallback === null) {
            return null;
        }

        $nextItem = $itemLookup->getItemForId(ItemId::newFromNumber($fallback->getValue()['numeric-id']));
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

    final protected function formatValue(DataValue $dataValue)
    {
        $value = $dataValue->getValue();
        if (is_scalar($value)) {
            return $value;
        }

        if (isset($value['time'])) {
            return $this->formatDate($value);
        }

        var_dump($value, get_class($this));
        die;
    }

    private function formatDate(array $value)
    {
        assert(array_key_exists('time', $value), 'Key "time" exists in ' . JsonUtil::encode($value));
        assert(array_key_exists('calendarmodel', $value), 'Key "calendarmodel" exists in ' . JsonUtil::encode($value));
        assert($value['timezone'] === 0);

        switch ($value['calendarmodel']) {
            // Julian calendar
            case 'http://www.wikidata.org/entity/Q1985786';
                $julian = new DateTimeImmutable(static::trimDateValue($value));
                $julianDays = juliantojd($julian->format('m'), $julian->format('d'), $julian->format('Y'));
                list($month, $day, $year) = explode('/', jdtogregorian($julianDays));
                $date = $julian->setDate($year, $month, $day)->format('Y-m-d\TH:i:s\Z');
                break;

            default:
                assert(
                    $value['calendarmodel'] === 'http://www.wikidata.org/entity/Q1985727',
                    'Expected gregorian calendar: ' . JsonUtil::encode($value)
                );

                $date = static::trimDateValue($value);
        }

        return $date;
    }

    private static function trimDateValue(array $value)
    {
        return trim($value['time'], '+');
    }

    protected function getBestStatementValue(Item $item, Property $property)
    {
        return $this->getStatementValue($item->getStatements()->getBestStatements(), $property)
            ?: $this->getStatementValue($item->getStatements(), $property);
    }

    protected function getStatementValue(StatementList $statements, Property $property)
    {
        $statements = $statements->getByPropertyId(PropertyId::newFromNumber($property->value()));

        return $statements->isEmpty() ? null : $statements->getMainSnaks()[0]->getDataValue();
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
