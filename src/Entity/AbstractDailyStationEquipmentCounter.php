<?php

namespace Roadsurfer\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType("SINGLE_TABLE")]
abstract class AbstractDailyStationEquipmentCounter
{
    #[ORM\ManyToOne(targetEntity: Station::class)]
    private Station $station;

    #[ORM\ManyToOne(targetEntity: EquipmentType::class)]
    private EquipmentType $equipmentType;

    # todo: add db-level check
    #[ORM\Column(type: "int")]
    private int $counter = 0;
}