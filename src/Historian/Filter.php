<?php
namespace Historian\Importer;

interface Filter
{
    public function matches(string $nativeId) : bool;
}
