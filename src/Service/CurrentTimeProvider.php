<?php


namespace Roadsurfer\Service;


use DateTime;

class CurrentTimeProvider implements CurrentTimeProviderInterface
{

    public function getCurrentTime(): DateTime
    {
        return new DateTime("now");
    }
}