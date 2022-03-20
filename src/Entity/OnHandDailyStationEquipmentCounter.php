<?php

namespace Roadsurfer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Repository\OnHandDailyStationEquipmentCounterRepository;

#[ORM\Entity(repositoryClass: OnHandDailyStationEquipmentCounterRepository::class)]
class OnHandDailyStationEquipmentCounter extends AbstractDailyStationEquipmentCounter
{

}