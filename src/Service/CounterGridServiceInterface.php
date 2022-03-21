<?php


namespace Roadsurfer\Service;


use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\OnHandDailyStationEquipmentCounter;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\Station;

interface CounterGridServiceInterface
{
    public function extendCounterGrid(int $daysInFutureToExtend): void;

    public function applyOrder(Order $order);

    /**
     * @param Station $station
     * @param string  $startDayCode
     * @param string  $endDayCode
     *
     * @return AbstractDailyStationEquipmentCounter[]
     */
    public function getAllCountersOnStation(Station $station, string $startDayCode, string $endDayCode);

    /**
     * @param Station $station
     * @param EquipmentType $equipmentType
     * @param string $startDayCode
     * @param string $endDayCode
     *
     * @return OnHandDailyStationEquipmentCounter[]
     */
    public function getOnHandCountersOnStationForEquipmentType(
        Station $station,
        EquipmentType $equipmentType,
        string $startDayCode,
        string $endDayCode
    );

    public function applyEquipmentShipment(Station $station, EquipmentType $equipmentType, string $dayCode, int $count);
}