<?php
namespace Historian\Importer\Util;

final class JsonUtil
{
    public static function encode($data)
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function decode($string)
    {
        return json_decode($string, true);
    }
}
