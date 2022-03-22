<?php


namespace Roadsurfer\Service;


use DateTime;

class CurrentTimeProvider implements CurrentTimeProviderInterface
{
    private ?DateTime $fixatedTime = null;

    public function getCurrentDateTime(): DateTime
    {
        if (!$this->fixatedTime) {
            return new DateTime("now");
        } else {
            return clone $this->fixatedTime;
        }
    }

    /**
     * @param DateTime|null $fixatedTime
     */
    public function setFixatedTime(?DateTime $fixatedTime): void
    {
        $this->fixatedTime = $fixatedTime;
    }
}