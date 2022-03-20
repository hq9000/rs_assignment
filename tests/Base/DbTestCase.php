<?php


namespace Roadsurfer\Tests\Base;


use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;

class DbTestCase extends KernelTestCase
{
    public function setUp(): void
    {
        $this->migrate(true);
        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->migrate(false);
        parent::tearDown();
    }

    private function migrate(bool $up = true)
    {
        $commandParts = [
            'php',
            'bin/console',
            'doctrine:migrations:migrate',
            '--no-interaction',
            '--allow-no-migration',
            '--env=test',
        ];

        if (!$up) {
            $commandParts[] = 'first';
        }

        $process  = new Process($commandParts);
        $exitCode = $process->run();

        if (0 !== $exitCode) {
            throw new Exception('migrations failed: ' . $process->getOutput() . $process->getErrorOutput());
        }
    }
}