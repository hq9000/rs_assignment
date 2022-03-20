<?php


namespace Roadsurfer\Service;


use DateTime;

class CurrentTimeProvider implements CurrentTimeProviderInterface
{

    public function getCurrentDateTime(): DateTime
    {
        return new DateTime("now");
    }
}