<?php

namespace Roadsurfer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Traits\HavingId;

# todo: add a db-level constraint preventing two counters to exist for the same day

#[ORM\Entity]
#[ORM\InheritanceType("SINGLE_TABLE")]
abstract class AbstractDailyStationEquipmentCounter
{
    use HavingId;

    # 20220321
    # 12345678 => 8 characters
    const LENGTH_OF_DAY_CODE = 8;

    #[ORM\ManyToOne(targetEntity: Station::class)]
    private Station $station;

    #[ORM\ManyToOne(targetEntity: EquipmentType::class)]
    private EquipmentType $equipmentType;

    #[ORM\Column(type: "integer", options: ["unsigned" => true])]
    private int $counter = 0;

    #[ORM\Column(type: "text", options: [
            "length" => self::LENGTH_OF_DAY_CODE,
        ]
    )]
    private string $dayCode;
}