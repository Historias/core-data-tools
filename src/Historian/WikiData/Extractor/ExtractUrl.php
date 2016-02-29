<?php
namespace Historian\Importer\WikiData\Extractor;

use Symfony\Component\PropertyAccess\PropertyPath;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Services\Lookup\ItemLookup;

class ExtractUrl implements Extractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[url]');
    }

    public function getValue(ItemLookup $itemLookup, Item $item)
    {
        return sprintf('https://www.wikidata.org/wiki/Q%d', $item->getId()->getNumericId());
    }
}
