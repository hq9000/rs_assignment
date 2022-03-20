<?php


namespace Roadsurfer\Service;


use Roadsurfer\Entity\Order;

interface CounterGridServiceInterface
{
    public function extendCounterGrid(): void;

    public function applyOrder(Order $order);
}