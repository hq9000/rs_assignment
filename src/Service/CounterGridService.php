<?php


namespace Roadsurfer\Service;


use Doctrine\ORM\EntityManagerInterface;
use Roadsurfer\DependencyInjection\CurrentTimeProviderAware;
use Roadsurfer\DependencyInjection\EntityManagerAware;

class CounterGridService implements CounterGridServiceInterface
{
    use CurrentTimeProviderAware;
    use EntityManagerAware;

    public function extendCounterGrid(): void
    {


    }
}