<?php


namespace Roadsurfer\Util;


use DateTime;

class DayCodeUtil
{
    # 20220321
    # 12345678 => 8 characters
    public const LENGTH_OF_DAY_CODE = 8;
    public const FORMAT = 'Ymd';

    public static function generateDayCode(DateTime $dateTime): string
    {
        return $dateTime->format(self::FORMAT);
    }

    public static function getNumberOfDaysCoveredByDayCodeRange(string $fromCode, string $toCode): int
    {
        $fromDateTime = DateTime::createFromFormat(self::FORMAT, $fromCode);
        $toDateTime   = DateTime::createFromFormat(self::FORMAT, $toCode);

        $interval = $toDateTime->diff($fromDateTime);
        return $interval->days + 1;

    }
}