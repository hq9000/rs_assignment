<?php


namespace Roadsurfer\Service;


use DateTime;

interface CurrentTimeProviderInterface
{
    public function getCurrentTime(): DateTime;

}