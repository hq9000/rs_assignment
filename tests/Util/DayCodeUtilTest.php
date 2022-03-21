<?php


namespace Roadsurfer\Tests\Util;


use PHPUnit\Framework\TestCase;
use Roadsurfer\Util\DayCodeUtil;

class DayCodeUtilTest extends TestCase
{
    public function testGetNumberOfDaysCoveredByDayCodeRange()
    {
        $this->assertEquals(2, DayCodeUtil::getNumberOfDaysCoveredByDayCodeRange('20220321', '20220322'));
        $this->assertEquals(3, DayCodeUtil::getNumberOfDaysCoveredByDayCodeRange('20220321', '20220323'));
        $this->assertEquals(1, DayCodeUtil::getNumberOfDaysCoveredByDayCodeRange('20220321', '20220321'));
    }
}