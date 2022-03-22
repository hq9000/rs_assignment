<?php


namespace Roadsurfer\Service;

use Roadsurfer\DependencyInjection\CurrentTimeProviderAware;
use Roadsurfer\DependencyInjection\EntityManagerAware;
use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;
use Roadsurfer\Entity\BookedDailyStationEquipmentCounter;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\OnHandDailyStationEquipmentCounter;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\Station;
use Roadsurfer\Repository\AbstractDailyStationEquipmentCounterRepository;
use Roadsurfer\Repository\OnHandDailyStationEquipmentCounterRepository;
use Roadsurfer\Util\DayCodeUtil;

class CounterGridService implements CounterGridServiceInterface
{

    use CurrentTimeProviderAware;
    use EntityManagerAware;

    public function extendCounterGrid(int $daysInFutureToExtend): void
    {
        $allStations       = $this->getAllStations();
        $allEquipmentTypes = $this->getAllEquipmentTypes();

        foreach ($allStations as $station) {
            foreach ($allEquipmentTypes as $type) {
                $this->extendInternal(
                    $station,
                    $type,
                    OnHandDailyStationEquipmentCounter::class,
                    $daysInFutureToExtend
                );
                $this->extendInternal(
                    $station,
                    $type,
                    BookedDailyStationEquipmentCounter::class,
                    $daysInFutureToExtend
                );
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

    public function applyEquipmentShipment(Station $station, EquipmentType $equipmentType, string $dayCode, int $count)
    {
        $this->changeOnHandEquipmentCount(
            $station,
            $dayCode,
            $equipmentType,
            $count
        );
    }

    public function getOnHandCountersOnStationForEquipmentType(
        Station $station,
        EquipmentType $equipmentType,
        string $startDayCode,
        string $endDayCode
    ) {
        /** @var OnHandDailyStationEquipmentCounterRepository $repo */
        $repo = $this->getEntityManager()->getRepository(OnHandDailyStationEquipmentCounter::class);

        return $repo->getCounters($station, $equipmentType, $startDayCode, $endDayCode);
    }

    /**
     * @inheritDoc
     */
    public function getAllCountersOnStation(Station $station, string $startDayCode, string $endDayCode)
    {
        /** @var AbstractDailyStationEquipmentCounterRepository $repo */
        $repo = $this->getEntityManager()->getRepository(AbstractDailyStationEquipmentCounter::class);
        return $repo->getCounters($station, null, $startDayCode, $endDayCode);
    }


    private function extendInternal(
        Station $station,
        EquipmentType $equipmentType,
        string $counterClass,
        int $daysInFutureToExtend
    ) {
        $currentDateTime = $this->getCurrentTimeProvider()->getCurrentDateTime();
        $lastDateTime    = $this->getCurrentTimeProvider()->getCurrentDateTime();
        $lastDateTime->modify('+' . $daysInFutureToExtend . ' days');

        /** @var AbstractDailyStationEquipmentCounterRepository $repo */
        $repo = $this->getEntityManager()->getRepository($counterClass);

        $startDateCode = DayCodeUtil::generateDayCode($currentDateTime);
        $endDateCode   = DayCodeUtil::generateDayCode($lastDateTime);

        $existingCounters = $repo->getCounters($station, $equipmentType, $startDateCode, $endDateCode);

        // building a hashmap for quicker lookup
        $existingCountersMap = [];
        foreach ($existingCounters as $counter) {
            $existingCountersMap[$counter->getDayCode()] = $counter;
        }

        $defaultCountValue = $this->determineDefaultValue($station, $equipmentType, $counterClass);

        while ($currentDateTime <= $lastDateTime) {
            $dayCode = DayCodeUtil::generateDayCode($currentDateTime);
            if (!isset($existingCountersMap[$dayCode])) {
                $this->createNewCounterEntity(
                    $station,
                    $equipmentType,
                    $counterClass,
                    $dayCode,
                    $defaultCountValue
                );
            }
            $currentDateTime->modify('+1 day');
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @return Iterable[Station]
     */
    private function getAllStations()
    {
        return $this->getEntityManager()->getRepository(Station::class)->findAll();
    }

    /**
     * @return Iterable[EquipmentType]
     */
    private function getAllEquipmentTypes()
    {
        return $this->getEntityManager()->getRepository(EquipmentType::class)->findAll();

    }

    private function changeOnHandEquipmentCount(
        Station $station,
        string $dayCode,
        EquipmentType $equipmentType,
        int $delta
    ) {
        /** @var AbstractDailyStationEquipmentCounterRepository $repo */
        $repo = $this->getEntityManager()->getRepository(OnHandDailyStationEquipmentCounter::class);
        $repo->incrementFutureCounters($station, $equipmentType, $dayCode, $delta);
    }

    private function changeBookedEquipmentCount(
        Station $station,
        string $dayCode,
        EquipmentType $getEquipmentType,
        int $count
    ) {
        /** @var AbstractDailyStationEquipmentCounterRepository $repo */
        $repo = $this->getEntityManager()->getRepository(BookedDailyStationEquipmentCounter::class);
        $repo->incrementOneCounter($station, $getEquipmentType, $dayCode, $count);
    }

    private function determineDefaultValue(
        Station $station,
        EquipmentType $equipmentType,
        string $counterClass
    ): int {

        if (BookedDailyStationEquipmentCounter::class == $counterClass) {
            return 0; // this kind of counter is always 0 in the beginning
        }

        assert(OnHandDailyStationEquipmentCounter::class == $counterClass);

        $repo = $this->getEntityManager()->getRepository($counterClass);
        /** @var AbstractDailyStationEquipmentCounter[] $lastCounterSearchResults */
        $lastCounterSearchResults = $repo->findBy(
            criteria: [
            'station'       => $station,
            'equipmentType' => $equipmentType,
        ],
            orderBy: [
            'dayCode' => 'desc',
        ],
            limit: 1
        );

        if (!$lastCounterSearchResults) {
            return 0;
        } else {
            return $lastCounterSearchResults[0]->getCount();
        }
    }

    private function createNewCounterEntity(
        Station $station,
        EquipmentType $equipmentType,
        string $counterClass,
        string $dayCode,
        int $defaultCountValue
    ) {
        /** @var AbstractDailyStationEquipmentCounter $counterEntity */
        $counterEntity = new $counterClass;

        assert($counterEntity instanceof AbstractDailyStationEquipmentCounter);

        $counterEntity->setDayCode($dayCode);
        $counterEntity->setEquipmentType($equipmentType);
        $counterEntity->setStation($station);
        $counterEntity->setCount($defaultCountValue);

        $this->getEntityManager()->persist($counterEntity);
    }

}