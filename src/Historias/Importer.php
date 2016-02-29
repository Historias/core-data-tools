<?php
namespace Historias\Importer;

interface Importer
{
    public function import(Filter $filter, ProgressLogger $progressLogger, ErrorLogger $errorLogger) : array;
}
