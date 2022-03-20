<?php


namespace Roadsurfer\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HavingName
{
    #[ORM\Column(
        type: "text",
        nullable: false
    )]
    protected string $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

}