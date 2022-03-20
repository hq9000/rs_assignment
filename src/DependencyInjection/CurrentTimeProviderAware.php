<?php


namespace Roadsurfer\DependencyInjection;


use Roadsurfer\Service\CurrentTimeProviderInterface;

trait CurrentTimeProviderAware
{
    private CurrentTimeProviderInterface $currentTimeProvider;

    /**
     * @return CurrentTimeProviderInterface
     */
    public function getCurrentTimeProvider(): CurrentTimeProviderInterface
    {
        return $this->currentTimeProvider;
    }

    /**
     * @param CurrentTimeProviderInterface $currentTimeProvider
     */
    public function setCurrentTimeProvider(CurrentTimeProviderInterface $currentTimeProvider): void
    {
        $this->currentTimeProvider = $currentTimeProvider;
    }

}