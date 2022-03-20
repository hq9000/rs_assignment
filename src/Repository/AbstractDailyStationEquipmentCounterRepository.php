<?php


namespace Roadsurfer\Repository;


use Doctrine\ORM\EntityRepository;
use Roadsurfer\Entity\AbstractDailyStationEquipmentCounter;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\OnHandDailyStationEquipmentCounter;
use Roadsurfer\Entity\Station;

class AbstractDailyStationEquipmentCounterRepository extends EntityRepository
{
    /**
     * @param Station       $station
     * @param EquipmentType $equipmentType
     * @param string        $startDayCode
     * @param string        $endDayCode
     *
     * @return AbstractDailyStationEquipmentCounter[]
     */
    public function getCounters(
        Station $station,
        EquipmentType $equipmentType,
        string $startDayCode,
        string $endDayCode
    ) {
        $qb = $this->createQueryBuilder('qb');
        $qb->select('c');

        $qb->andWhere('c.station = :station');
        $qb->andWhere('c.equipmentType = :equipmentType');
        $qb->andWhere($qb->expr()->between('c.dayCode', ':from', ':to'));

        $qb->setParameters(
            [
                'station'       => $station,
                'equipmentType' => $equipmentType,
                'from'          => $startDayCode,
                'to'            => $endDayCode,
            ]
        );

        return $qb->getQuery()->execute();
    }

    public function incrementFutureCounters(
        Station $station,
        EquipmentType $equipmentType,
        string $fromDayCode,
        int $delta
    ) {
        $qb = $this->createQueryBuilder('qb');
        $qb->update('c');
        $qb->where('c.dayCode >= :from_day_code');
        $qb->andWhere('c.equipmentType = :equipment_type');
        $qb->andWhere('c.station = :station');
        $qb->set('c.count', 'c.count + :delta');

        $qb->setParameters(
            [
                'from_day_code'  => $fromDayCode,
                'equipment_type' => $equipmentType,
                'station'        => $station,
                'delta'          => $delta,
            ]
        );

        $qb->getQuery()->execute();
    }

    public function incrementOneCounter(Station $station, EquipmentType $equipmentType, string $dayCode, int $delta)
    {
        $qb = $this->createQueryBuilder('qb');
        $qb->update('c');
        $qb->where('c.dayCode = :day_code');
        $qb->andWhere('c.equipmentType = :equipment_type');
        $qb->andWhere('c.station = :station');
        $qb->set('c.count', 'c.count + :delta');

        $qb->setParameters(
            [
                'day_code'       => $dayCode,
                'equipment_type' => $equipmentType,
                'station'        => $station,
                'delta'          => $delta,
            ]
        );

        $qb->getQuery()->execute();
    }
}