<?php


namespace Roadsurfer\DependencyInjection;


use Roadsurfer\Service\CounterGridServiceInterface;

trait CounterGridServiceAware
{
    private CounterGridServiceInterface $counterGridService;

    /**
     * @return CounterGridServiceInterface
     */
    public function getCounterGridService(): CounterGridServiceInterface
    {
        return $this->counterGridService;
    }

    /**
     * @param CounterGridServiceInterface $counterGridExtensionService
     */
    public function setCounterGridService(CounterGridServiceInterface $counterGridExtensionService
    ): void {
        $this->counterGridService = $counterGridExtensionService;
    }
}