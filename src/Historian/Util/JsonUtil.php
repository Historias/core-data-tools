<?php
namespace Historian\Importer\Util;

final class JsonUtil
{
    public static function encode($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public static function decode($string)
    {
        return json_decode($string, true);
    }
}
