<?php


namespace Roadsurfer\Tests\Form;


use DateTime;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\Station;
use Roadsurfer\Form\OrderType;
use Roadsurfer\Service\CurrentTimeProvider;
use Roadsurfer\Tests\Base\DbTestCase;
use Roadsurfer\Util\DayCodeUtil;
use Roadsurfer\Util\FormErrorPresenterUtil;
use Symfony\Component\Form\FormFactory;

class OrderTypeTest extends DbTestCase
{

    private EquipmentType $toothBrush;
    private EquipmentType $towel;

    private Station $berlin;
    private Station $frankfurt;

    private string $todayDayCode;
    private string $tomorrowDayCode;

    protected function setUp(): void
    {
        $this->toothBrush = $this->createEquipmentType('Tooth brush');
        $this->towel      = $this->createEquipmentType('Towel');

        $this->berlin    = $this->createStation('Berlin');
        $this->frankfurt = $this->createStation('Frankfurt');

        $todayDateTime    = DateTime::createFromFormat('Y-m-d', '2022-04-15');;
        $tomorrowDateTime = clone $todayDateTime;
        $tomorrowDateTime->modify('+1 day');

        $this->todayDayCode    = DayCodeUtil::generateDayCode($todayDateTime);
        $this->tomorrowDayCode = DayCodeUtil::generateDayCode($tomorrowDateTime);

        $currentTimeProvider = $this->getContainer()->get(CurrentTimeProvider::class);
        $currentTimeProvider->setFixatedTime($todayDateTime);

        $counterGridService = $this->getCounterGridServiceFromContainer();
        $counterGridService->extendCounterGrid(3);
        $counterGridService->applyEquipmentShipment($this->berlin, $this->toothBrush, $this->todayDayCode, 100);
        $counterGridService->applyEquipmentShipment($this->berlin, $this->towel, $this->todayDayCode, 100);
        $counterGridService->getEntityManager()->clear();

        parent::setUp();
    }

    public function testHappySubmission()
    {

        /** @var FormFactory $formFactory */
        $formFactory = static::getContainer()->get('form.factory');
        $order       = new Order;

        $form = $formFactory->create(OrderType::class, $order);

        $data = [
            'startStation'           => $this->berlin->getId(),
            'endStation'             => $this->frankfurt->getId(),
            'startDayCode'           => $this->todayDayCode,
            'endDayCode'             => $this->tomorrowDayCode,
            'orderEquipmentCounters' => [
                [
                    'equipmentType' => $this->toothBrush->getId(),
                    'count'         => 10,
                ],
                [
                    'equipmentType' => $this->towel->getId(),
                    'count'         => 20,
                ],
            ],
        ];

        $form->submit($data);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertTrue($form->isSynchronized());
    }

    public function testSubmissionWithInvalidEntityIds()
    {
        /** @var FormFactory $formFactory */
        $formFactory = static::getContainer()->get('form.factory');
        $order       = new Order;

        $form = $formFactory->create(OrderType::class, $order);

        $data = [
            'startStation'           => $this->berlin->getId() + 100,
            'endStation'             => $this->frankfurt->getId() + 100,
            'startDayCode'           => $this->todayDayCode,
            'endDayCode'             => $this->tomorrowDayCode,
            'orderEquipmentCounters' => [
                [
                    'equipmentType' => $this->toothBrush->getId() + 100,
                    'count'         => 10,
                ],
                [
                    'equipmentType' => $this->towel->getId() + 100,
                    'count'         => 20,
                ],
            ],
        ];

        $form->submit($data);

        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
        $errors = FormErrorPresenterUtil::presentErrors($form);

        $expectedErrors = [
            0 => 'children[startStation]:This value is not valid.',
            1 => 'children[endStation]:This value is not valid.',
            2 => 'children[orderEquipmentCounters].children[0].children[equipmentType]:This value is not valid.',
            3 => 'children[orderEquipmentCounters].children[1].children[equipmentType]:This value is not valid.',
        ];

        $this->assertEquals($expectedErrors, $errors);
    }


    public function testSubmissionWithValidEntitiesButFailedAvailabilityCheck()
    {
        /** @var FormFactory $formFactory */
        $formFactory = static::getContainer()->get('form.factory');
        $order       = new Order;

        $form = $formFactory->create(OrderType::class, $order);

        $data = [
            'startStation'           => $this->berlin->getId(),
            'endStation'             => $this->frankfurt->getId(),
            'startDayCode'           => $this->todayDayCode,
            'endDayCode'             => $this->tomorrowDayCode,
            'orderEquipmentCounters' => [
                [
                    'equipmentType' => $this->toothBrush->getId(),
                    'count'         => 10000,
                ],
                [
                    'equipmentType' => $this->towel->getId(),
                    'count'         => 20000,
                ],
            ],
        ];

        $form->submit($data);

        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
        $errors = FormErrorPresenterUtil::presentErrors($form);

        $expectedErrors = [
            0 => 'root:Placing this order would make station "Berlin" run out of equipment "Tooth brush" on day 20220415!',
            1 => 'root:Placing this order would make station "Berlin" run out of equipment "Towel" on day 20220415!',
        ];

        $this->assertEquals($expectedErrors, $errors);
    }
}
