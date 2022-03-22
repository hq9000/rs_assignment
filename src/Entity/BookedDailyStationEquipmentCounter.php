<?php

namespace Roadsurfer\Entity;

use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Repository\BookedDailyStationEquipmentCounterRepository;

#[ORM\Entity(repositoryClass: BookedDailyStationEquipmentCounterRepository::class)]
class BookedDailyStationEquipmentCounter extends  AbstractDailyStationEquipmentCounter
{
    public function getReportLabel(): string
    {
        return "booked";
    }
}