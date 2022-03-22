<?php


namespace Roadsurfer\Service;


use DateTime;

interface CurrentTimeProviderInterface
{
    public function getCurrentDateTime(): DateTime;

}