<?php


namespace Roadsurfer\Command;


use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\Util\DatePolicy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtendCounterGridCommand extends Command
{
    use CounterGridServiceAware;

    protected static $defaultName = 'roadsurfer:extend_grid';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getCounterGridService()->extendCounterGrid(DatePolicy::NUM_FUTURE_DAYS_TO_ENSURE_COUNTER_GRID_FOR);
        return 0;
    }

}