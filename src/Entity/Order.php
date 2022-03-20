<?php

namespace Roadsurfer\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Traits\HavingId;
use Roadsurfer\Repository\OrderRepository;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order
{
    use HavingId;

    #[NotNull]
    private ?DateTime $startDate = null;

    #[NotNull]
    private ?DateTime $endDate = null;

    #[NotNull]
    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $startStation = null;

    #[NotNull]
    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $endStation = null;
}