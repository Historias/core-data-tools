<?php
namespace Historian\Importer\WikiData\Extractor;

use Symfony\Component\PropertyAccess\PropertyPath;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Services\Lookup\ItemLookup;

class ExtractName implements Extractor
{
    public function getPath() : PropertyPath
    {
        return new PropertyPath('[name]');
    }

    public function getValue(ItemLookup $itemLookup, Item $item)
    {
        return $item->getFingerprint()->getLabel('en')->getText();
    }
}
