<?php


namespace Roadsurfer\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Mixin\HavingId;
use Roadsurfer\Repository\OrderRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class OrderEquipmentCounter
{
    use HavingId;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: "orderEquipmentCounters")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull()]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: EquipmentType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?EquipmentType $equipmentType = null;

    #[ORM\Column(type: "integer")]
    #[Assert\GreaterThan(0)]
    private int $count = 0;

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return EquipmentType|null
     */
    public function getEquipmentType(): ?EquipmentType
    {
        return $this->equipmentType;
    }

    /**
     * @param EquipmentType|null $equipmentType
     */
    public function setEquipmentType(?EquipmentType $equipmentType): void
    {
        $this->equipmentType = $equipmentType;
    }

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

}