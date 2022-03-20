<?php

namespace Roadsurfer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Mixin\HavingId;
use Roadsurfer\Util\DayCodeUtil;

#[ORM\Entity]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\UniqueConstraint(columns: ["dtype", "station_id", "equipment_type_id", "day_code"])]
abstract class AbstractDailyStationEquipmentCounter
{
    use HavingId;

    #[ORM\ManyToOne(targetEntity: Station::class)]
    private Station $station;

    #[ORM\ManyToOne(targetEntity: EquipmentType::class)]
    private EquipmentType $equipmentType;

    #[ORM\Column(type: "integer", options: ["unsigned" => true])]
    private int $counter = 0;

    #[ORM\Column(type: "text", options: [
            "length" => DayCodeUtil::LENGTH_OF_DAY_CODE,
        ]
    )]
    private string $dayCode;
}