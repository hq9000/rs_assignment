<?php

namespace Roadsurfer\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Mixin\HavingId;
use Roadsurfer\Repository\OrderRepository;
use Roadsurfer\Util\DayCodeUtil;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order
{
    use HavingId;

    #[NotNull]
    #[ORM\Column(type: "text", nullable: false, options: [
        "length" => DayCodeUtil::LENGTH_OF_DAY_CODE,
    ])]
    private ?DateTime $startDayCode = null;

    #[NotNull]
    #[ORM\Column(type: "text", nullable: false, options: [
        "length" => DayCodeUtil::LENGTH_OF_DAY_CODE,
    ])]
    private ?string $endDayCode = null;

    #[NotNull]
    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $startStation = null;

    #[NotNull]
    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $endStation = null;
}