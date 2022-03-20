<?php


namespace Roadsurfer\Tests\Base;


use Doctrine\ORM\EntityManager;
use Exception;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\Station;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Process\Process;

class DbTestCase extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    protected static $entityManager;

    public static function setUpBeforeClass(): void
    {
        self::migrate(true);
        static::bootKernel();
        static::$entityManager = static::$kernel->getContainer()
                                                ->get('doctrine')
                                                ->getManager();
    }

    public static function tearDownAfterClass(): void
    {
        self::migrate(false);
    }


    private static function migrate(bool $up = true)
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

    protected function createStation(string $name): Station
    {
        $station = new Station();
        $station->setName($name);

        self::$entityManager->persist($station);
        self::$entityManager->flush();
        return $station;
    }

    protected function createEquipmentType(string $name): EquipmentType
    {
        $type = new EquipmentType();
        $type->setName($name);

        self::$entityManager->persist($type);
        self::$entityManager->flush();
        return $type;
    }

    protected function removeAll(string $class)
    {
        $qb = self::$entityManager->getRepository($class)->createQueryBuilder('qb');

        $qb->delete();
        $qb->getQuery()->execute();

        self::$entityManager->clear();
    }

    protected function getEntityCount(string $class): int
    {
        return self::$entityManager->getRepository($class)->count([]);
    }
}