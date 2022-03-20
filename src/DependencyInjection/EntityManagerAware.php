<?php


namespace Roadsurfer\DependencyInjection;


use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerAware
{
    private EntityManagerInterface $entityManager;

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

}