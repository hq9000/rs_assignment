<?php


namespace Roadsurfer\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Traits\HavingId;
use Roadsurfer\Repository\OrderRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class OrderEquipmentCounter
{
    use HavingId;

    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: EquipmentType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?EquipmentType $equipmentType = null;

    #[ORM\Column(type: "integer")]
    #[Assert\GreaterThan(0)]
    private int $count = 0;

}