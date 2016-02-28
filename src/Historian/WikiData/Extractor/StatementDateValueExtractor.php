<?php
namespace Historian\Importer\WikiData\Extractor;

use DateTimeImmutable;
use Historian\Importer\Util\JsonUtil;

abstract class StatementDateValueExtractor extends StatementValueExtractor
{
    final protected function formatValue($value)
    {
        assert(array_key_exists('time', $value), 'Key "time" exists in ' . JsonUtil::encode($value));
        assert(array_key_exists('calendarmodel', $value), 'Key "calendarmodel" exists in ' . JsonUtil::encode($value));
        assert($value['timezone'] === 0);

        switch ($value['calendarmodel']) {
            // Julian calendar
            case 'http://www.wikidata.org/entity/Q1985786';
                $julian = new DateTimeImmutable(static::trimValue($value));
                $julianDays = juliantojd($julian->format('m'), $julian->format('d'), $julian->format('Y'));
                list($month, $day, $year) = explode('/', jdtogregorian($julianDays));
                $date = $julian->setDate($year, $month, $day)->format('Y-m-d\TH:i:s\Z');
                break;

            default:
                assert(
                    $value['calendarmodel'] === 'http://www.wikidata.org/entity/Q1985727',
                    'Expected gregorian calendar: ' . JsonUtil::encode($value)
                );

                $date = static::trimValue($value);
        }

        return $date;
    }

    private static function trimValue(array $value)
    {
        return trim($value['time'], '+');
    }
}
