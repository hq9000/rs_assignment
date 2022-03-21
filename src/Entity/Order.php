<?php

namespace Roadsurfer\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
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
    #[ORM\Column(type: "string", length: DayCodeUtil::LENGTH_OF_DAY_CODE, nullable: false)]
    private ?string $startDayCode = null;

    #[NotNull]
    #[ORM\Column(type: "string", length: DayCodeUtil::LENGTH_OF_DAY_CODE, nullable: false)]
    private ?string $endDayCode = null;

    #[NotNull]
    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $startStation = null;

    #[NotNull]
    #[ORM\ManyToOne(targetEntity: Station::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Station $endStation = null;


    /**
     * @var OrderEquipmentCounter[]
     */
    #[ORM\OneToMany(mappedBy: "order", targetEntity: OrderEquipmentCounter::class)]
    private Collection|array $orderEquipmentCounters;

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