<?php


namespace Roadsurfer\Tests\Service;

use Roadsurfer\Tests\Base\DbTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class ExperimentTest extends DbTestCase
{

    public function testSomething()
    {
        $env = getenv('APP_ENV');
        $this->assertEquals('test', $env);
    }

}