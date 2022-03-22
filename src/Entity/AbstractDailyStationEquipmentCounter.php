<?php

namespace Roadsurfer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Mixin\HavingId;
use Roadsurfer\Repository\AbstractDailyStationEquipmentCounterRepository;
use Roadsurfer\Util\DayCodeUtil;

#[ORM\Entity(repositoryClass: AbstractDailyStationEquipmentCounterRepository::class)]
#[ORM\Table(name: "daily_station_equipment_counters")]
#[ORM\DiscriminatorColumn(name: "dtype", type: "string", length: 6)]
#[ORM\DiscriminatorMap([
    "onhand" => OnHandDailyStationEquipmentCounter::class,
    "booked" => BookedDailyStationEquipmentCounter::class,
])]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\UniqueConstraint(columns: ["dtype", "station_id", "equipment_type_id", "day_code"])]
#[ORM\Index(columns: ["station_id", "day_code"])]
abstract class AbstractDailyStationEquipmentCounter
{
    use HavingId;

    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Station $station;

    #[ORM\ManyToOne(targetEntity: EquipmentType::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private EquipmentType $equipmentType;

    #[ORM\Column(type: "integer", options: ["unsigned" => true])]
    private int $count = 0;

    #[ORM\Column(type: "string", length: DayCodeUtil::LENGTH_OF_DAY_CODE)]
    private string $dayCode;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @return EquipmentType
     */
    public function getEquipmentType(): EquipmentType
    {
        return $this->equipmentType;
    }

    /**
     * @param EquipmentType $equipmentType
     */
    public function setEquipmentType(EquipmentType $equipmentType): void
    {
        $this->equipmentType = $equipmentType;
    }

    /**
     * @return Station
     */
    public function getStation(): Station
    {
        return $this->station;
    }

    /**
     * @param Station $station
     */
    public function setStation(Station $station): void
    {
        $this->station = $station;
    }

    /**
     * @return string
     */
    public function getDayCode(): string
    {
        return $this->dayCode;
    }

    /**
     * @param string $dayCode
     */
    public function setDayCode(string $dayCode): void
    {
        $this->dayCode = $dayCode;
    }

    abstract public function getReportLabel(): string;
}