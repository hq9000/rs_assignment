<?php


namespace Roadsurfer\Util;



use DateTime;

class DayCodeUtil
{
    # 20220321
    # 12345678 => 8 characters
    public const LENGTH_OF_DAY_CODE = 8;

    public static function generateDayCode(DateTime $dateTime): string
    {
        return $dateTime->format('Ymd');
    }
}