<?php


namespace Roadsurfer\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;


trait HavingId
{

    #[ORM\Column(
        type: "bigint",
        nullable: false,
        options: ["unsigned" => true]
    )]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Id]
    private int $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}