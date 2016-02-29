<?php
namespace Historias\Importer\WikiData\Extractor;

use Symfony\Component\PropertyAccess\PropertyPath;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Services\Lookup\ItemLookup;

interface Extractor
{
    public function getPath() : PropertyPath;

    public function getValue(ItemLookup $itemLookup, Item $item);
}
