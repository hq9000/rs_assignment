<?php


namespace Roadsurfer\Controller;

use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        return new JsonResponse(['hello' => 'world']);
    }

}