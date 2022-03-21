<?php


namespace Roadsurfer\Controller;

use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;
use Roadsurfer\Entity\Station;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class ApiController
{
    use CounterGridServiceAware;

    #[Route('/equipment_availability/{station}/{startDayCode}/{endDayCode}',
        name: 'requirement_availability', requirements: [
            'startDayCode' => "\d+",
            'endDayCode'   => "\d+",
        ])
    ]
    public function getEquipmentAvailability(
        Station $station,
        string $startDayCode,
        string $endDayCode
    ): JsonResponse {
        $allCounters = $this->getCounterGridService()->getAllCountersOnStation($station, $startDayCode, $endDayCode);
        return new JsonResponse($this->presentCountersAsReport($allCounters));
    }

    /**
     * @param AbstractDailyStationEquipmentCounter[] $allCounters
     *
     * @return array
     */
    private function presentCountersAsReport($allCounters): array
    {
        $report = [];

        foreach ($allCounters as $counter) {
            $report[$counter->getDayCode()][$counter->getReportLabel()] = $counter->getCount();
        }

        return $report;
    }

}