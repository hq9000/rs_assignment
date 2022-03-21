<?php


namespace Roadsurfer\Controller;

use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\DependencyInjection\EntityManagerAware;
use Roadsurfer\DependencyInjection\FormFactoryAware;
use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\Station;
use Roadsurfer\Form\OrderType;
use Roadsurfer\Util\ReportDataProducer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class ApiController
{
    use CounterGridServiceAware;
    use FormFactoryAware;
    use EntityManagerAware;

    #[Route('/equipment_counters/{station}/{startDayCode}/{endDayCode}',
        name: 'requirement_availability',
        requirements: [
            'startDayCode' => "\d+",
            'endDayCode'   => "\d+"
        ],
        methods: ["GET"]
    )]
    public function getEquipmentAvailabilityReport(
        Station $station,
        int $startDayCode,
        int $endDayCode
    ): JsonResponse {
        $this->validateDayCode($startDayCode);
        $this->validateDayCode($endDayCode);
        $allCounters = $this->getCounterGridService()->getAllCountersOnStation($station, $startDayCode, $endDayCode);
        return new JsonResponse($this->presentCountersAsReport($allCounters));
    }

    #[Route('/orders',
        name: 'post_orders',
        methods: ["POST"]
    )]
    public function postNewOrder(
        Request $request
    ): JsonResponse {
         $order = new Order();
         $form = $this->getFormFactory()->create(OrderType::class, $order);

         $form->handleRequest($request);

         if ($form->isSubmitted() and $form->isValid()) {
             $this->getEntityManager()->wrapInTransaction(function() use ($order) {
                 $this->getCounterGridService()->applyOrder($order);
                 $this->getEntityManager()->persist($order);
                 $this->getEntityManager()->flush();
             });
             return new JsonResponse([], Response::HTTP_CREATED);
         } else {
             return new JsonResponse($this->createDataOnInvalidForm($form), Response::HTTP_BAD_REQUEST);
         }
    }

    /**
     * @param AbstractDailyStationEquipmentCounter[] $allCounters
     *
     * @return array
     */
    private function presentCountersAsReport($allCounters): array
    {
         return (new ReportDataProducer())->produceReportData($allCounters);
    }

    private function validateDayCode(int $code)
    {
        if ($code < 20000000 or $code > 30000000) {
            throw new NotFoundHttpException();
        }
    }

    private function createDataOnInvalidForm(FormInterface $form): array
    {
        return [];
    }

}