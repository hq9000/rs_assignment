<?php


namespace Roadsurfer\Util;


use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;

class ReportDataProducer
{
    /**
     * @param AbstractDailyStationEquipmentCounter[] $allCounters
     *
     * @return array
     */
    public function produceReportData($allCounters): array
    {
        $report = [];

        foreach ($allCounters as $counter) {
            $report[
                $counter->getStation()->getName()
            ][
                $counter->getEquipmentType()->getName()
            ][
                $counter->getDayCode()
            ][
                $counter->getReportLabel()
            ] = $counter->getCount();
        }

        return $report;
    }
}