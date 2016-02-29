<?php
namespace Historias\Importer;

interface Filter
{
    public function matches(string $nativeId) : bool;
}
