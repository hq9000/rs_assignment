<?php

namespace Roadsurfer\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Roadsurfer\Entity\Mixin\HavingId;
use Roadsurfer\Repository\OrderRepository;
use Roadsurfer\Util\DayCodeUtil;
use Roadsurfer\Validator\OrderConstraint;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: "orders")]
#[OrderConstraint]
class Order
{
    use HavingId;

    #[Assert\NotNull]
    #[ORM\Column(type: "string", length: DayCodeUtil::LENGTH_OF_DAY_CODE, nullable: false)]
    private ?string $startDayCode = null;

    #[Assert\NotNull]
    #[ORM\Column(type: "string", length: DayCodeUtil::LENGTH_OF_DAY_CODE, nullable: false)]
    private ?string $endDayCode = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $startStation = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $endStation = null;


    /**
     * @var OrderEquipmentCounter[]
     */
    #[ORM\OneToMany(mappedBy: "order", targetEntity: OrderEquipmentCounter::class, cascade: ["PERSIST"])]
    #[Assert\Valid()]
    private Collection|array $orderEquipmentCounters = [];

    /**
     * @return OrderEquipmentCounter[]
     */
    public function getOrderEquipmentCounters()
    {
        return $this->orderEquipmentCounters;
    }

    /**
     * @param OrderEquipmentCounter[] $orderEquipmentCounters
     */
    public function setOrderEquipmentCounters(Collection|array $orderEquipmentCounters): void
    {
        foreach ($orderEquipmentCounters as $counter) {
            $counter->setOrder($this);
        }
        $this->orderEquipmentCounters = $orderEquipmentCounters;
    }

    /**
     * @return Station|null
     */
    public function getStartStation(): ?Station
    {
        return $this->startStation;
    }

    /**
     * @param Station|null $startStation
     */
    public function setStartStation(?Station $startStation): void
    {
        $this->startStation = $startStation;
    }

    /**
     * @return Station|null
     */
    public function getEndStation(): ?Station
    {
        return $this->endStation;
    }

    /**
     * @param Station|null $endStation
     */
    public function setEndStation(?Station $endStation): void
    {
        $this->endStation = $endStation;
    }

    /**
     * @return string|null
     */
    public function getStartDayCode(): ?string
    {
        return $this->startDayCode;
    }

    /**
     * @param string|null $startDayCode
     */
    public function setStartDayCode(?string $startDayCode): void
    {
        $this->startDayCode = $startDayCode;
    }

    /**
     * @return string|null
     */
    public function getEndDayCode(): ?string
    {
        return $this->endDayCode;
    }

    /**
     * @param string|null $endDayCode
     */
    public function setEndDayCode(?string $endDayCode): void
    {
        $this->endDayCode = $endDayCode;
    }
}