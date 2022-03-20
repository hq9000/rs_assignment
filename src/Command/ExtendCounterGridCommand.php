<?php


namespace Roadsurfer\Command;


use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\Service\CounterGridServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtendCounterGridCommand extends Command
{
    use CounterGridServiceAware;

    protected static $defaultName = 'roadsurfer:extend_grid';

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getCounterGridService()->extendCounterGrid();
        return 0;
    }

}