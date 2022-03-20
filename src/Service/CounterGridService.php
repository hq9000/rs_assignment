<?php


namespace Roadsurfer\Service;

use Roadsurfer\DependencyInjection\CurrentTimeProviderAware;
use Roadsurfer\DependencyInjection\EntityManagerAware;
use Roadsurfer\Entity\BookedDailyStationEquipmentCounter;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\OnHandDailyStationEquipmentCounter;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\Station;

class CounterGridService implements CounterGridServiceInterface
{

    use CurrentTimeProviderAware;
    use EntityManagerAware;

    public function extendCounterGrid(): void
    {
        $allStations       = $this->getAllStations();
        $allEquipmentTypes = $this->getAllEquipmentTypes();

        foreach ($allStations as $station) {
            foreach ($allEquipmentTypes as $type) {
                $this->extendInternal($station, $type, OnHandDailyStationEquipmentCounter::class);
                $this->extendInternal($station, $type, BookedDailyStationEquipmentCounter::class);
            }
        }
    }

    public function applyOrder(Order $order)
    {
        foreach ($order->getOrderEquipmentCounters() as $orderEquipmentCounter) {
            $this->changeOnHandEquipmentCount(
                $order->getStartStation(),
                $order->getStartDayCode(),
                $orderEquipmentCounter->getEquipmentType(),
                -$orderEquipmentCounter->getCount()
            );

            $this->changeOnHandEquipmentCount(
                $order->getEndStation(),
                $order->getEndDayCode(),
                $orderEquipmentCounter->getEquipmentType(),
                $orderEquipmentCounter->getCount()
            );

            $this->changeBookedEquipmentCount(
                $order->getStartStation(),
                $order->getStartDayCode(),
                $orderEquipmentCounter->getEquipmentType(),
                $orderEquipmentCounter->getCount()
            );
        }
    }

    public function getOnHandCounters(
        Station $station,
        EquipmentType $equipmentType,
        string $startDayCode,
        string $endDayCode
    ) {
        $repo = $this->getEntityManager()->getRepository(OnHandDailyStationEquipmentCounter::class);

        return $repo->findBy
    }


    private function extendInternal(
        Station $station,
        EquipmentType $equipmentType,
        string $counterClass
    ) {

        $repo = $this->getEntityManager()->getRepository($counterClass);

        $lastCell = $repo->findBy(
            criteria: [
            'station'       => $station,
            'equipmentType' => $equipmentType,
        ],
            orderBy: [
            'dayCode' => 'desc',
        ],
            limit: 1
        );


        // 1. find the last cell in the grid
        // 2. create needed cells into the future, the value of counter is either 0
        //    or the value in the last cell

    }

    /**
     * @return Iterable[Station]
     */
    private function getAllStations()
    {

    }

    /**
     * @return Iterable[EquipmentType]
     */
    private function getAllEquipmentTypes()
    {

    }

    private function changeOnHandEquipmentCount(
        Station $station,
        string $dayCode,
        EquipmentType $equipmentType,
        int $delta
    ) {
    }

    private function changeBookedEquipmentCount(
        Station $station,
        string $dayCode,
        EquipmentType $getEquipmentType,
        int $count
    ) {
    }

}