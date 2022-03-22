<?php

namespace Roadsurfer\DataFixtures;

use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Roadsurfer\DependencyInjection\CounterGridServiceAware;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\Station;
use Roadsurfer\Util\DayCodeUtil;

class AppFixtures extends Fixture
{
    use CounterGridServiceAware;

    public function load(ObjectManager $manager): void
    {
        $chair = new EquipmentType();
        $chair->setName("Chair");
        $manager->persist($chair);

        $towel = new EquipmentType();
        $towel->setName("Towel");
        $manager->persist($towel);

        $berlin = new Station();
        $berlin->setName("Berlin");
        $manager->persist($berlin);

        $frankfurt = new Station();
        $frankfurt->setName("Frankfurt");
        $manager->persist($frankfurt);

        $manager->flush();

        $this->getCounterGridService()->extendCounterGrid(365);

        foreach ([$berlin, $frankfurt] as $station) {
            foreach ([$chair, $towel] as $equipmentType) {
                $this->getCounterGridService()->applyEquipmentShipment(
                    $station,
                    $equipmentType,
                    DayCodeUtil::generateDayCode(new DateTime("now")),
                    100
                );
            }
        }
    }
}
