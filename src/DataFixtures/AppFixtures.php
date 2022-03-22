<?php

namespace Roadsurfer\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\Station;

class AppFixtures extends Fixture
{
    use CounterGridServiceAware;

    public function load(ObjectManager $manager): void
    {
        $chair = new EquipmentType();
        $chair->setName("Tooth brush");
        $manager->persist($chair);

        $toilet = new EquipmentType();
        $toilet->setName("Towel");
        $manager->persist($toilet);

        $berlin = new Station();
        $berlin->setName("Berlin");
        $manager->persist($berlin);

        $berlin = new Station();
        $berlin->setName("Frankfurt");
        $manager->persist($berlin);

        $manager->flush();

        $this->getCounterGridService()->extendCounterGrid(365);
    }
}
