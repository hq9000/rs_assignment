<?php


namespace Roadsurfer\Controller;

use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;
use Roadsurfer\Entity\Station;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        int $startDayCode,
        int $endDayCode
    ): JsonResponse {

        $this->validateDayCode($startDayCode);
        $this->validateDayCode($endDayCode);

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

    }

    private function validateDayCode(int $code)
    {
        if ($code < 20000000 or $code > 30000000) {
            throw new NotFoundHttpException();
        }
    }

}