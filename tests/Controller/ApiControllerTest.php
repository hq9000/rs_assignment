<?php


namespace Roadsurfer\Tests\Controller;


use DateTime;
use Roadsurfer\Entity\EquipmentType;
use Roadsurfer\Entity\Order;
use Roadsurfer\Entity\OrderEquipmentCounter;
use Roadsurfer\Entity\Station;
use Roadsurfer\Service\CounterGridService;
use Roadsurfer\Service\CurrentTimeProvider;
use Roadsurfer\Tests\Base\DbTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
use Symfony\Component\HttpFoundation\Response;

class ApiControllerTest extends DbTestCase
{
    use WebTestAssertionsTrait;

    private const NOW_DATETIME_STR = '2022-03-22 06:32:33';
    private const DAYCODE_1 = '20220322';
    private const DAYCODE_2 = '20220323';
    private const DAYCODE_3 = '20220324';

    protected Station $berlin;
    protected Station $frankfurt;

    protected EquipmentType $toothBrush;
    protected EquipmentType $towel;

    public function setUp(): void
    {
        $this->berlin     = $this->createStation('Berlin');
        $this->frankfurt  = $this->createStation('Frankfurt');
        $this->toothBrush = $this->createEquipmentType('Tooth brush');
        $this->towel      = $this->createEquipmentType('Towel');

        $currentTimeProvider = static::getContainer()->get(CurrentTimeProvider::class);
        $gridService         = static::getContainer()->get(CounterGridService::class);

        $fixatedDate = DateTime::createFromFormat('Y-m-d H:i:s', self::NOW_DATETIME_STR);
        $currentTimeProvider->setFixatedTime($fixatedDate);
        $gridService->extendCounterGrid(2);
        parent::setUp();
    }

    public function testGetUsageReport()
    {
        $client = self::$kernel->getContainer()->get('test.client');
        $client->request('GET', '/stations/' . $this->berlin->getId() . '/equipment_usage_report');

        /** @var Response $response */
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());

        $bodyArr = json_decode($response->getContent(), true);

        $expectedBodyArr = [
            'Berlin' =>
                [
                    'Tooth brush' =>
                        [
                            20220322 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                        ],
                    'Towel'       =>
                        [
                            20220322 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                            20220324 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                        ],
                ],
        ];

        $this->assertEquals($expectedBodyArr, $bodyArr);

        $client->request(
            'GET',
            '/stations/' . $this->berlin->getId() . '/equipment_usage_report?from=20220323&to=20220323'
        );
        /** @var Response $response */
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $bodyArr = json_decode($response->getContent(), true);

        $expectedBodyArr = [
            'Berlin' =>
                [
                    'Tooth brush' =>
                        [
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                        ],
                    'Towel'       =>
                        [
                            20220323 =>
                                [
                                    'on hand' => 0,
                                    'booked'  => 0,
                                ],
                        ],
                ],
        ];

        $this->assertEquals($expectedBodyArr, $bodyArr);
    }

    public function testGetUsageReportTooWideRange()
    {
        $client = self::$kernel->getContainer()->get('test.client');
        $client->request(
            'GET',
            '/stations/' . $this->berlin->getId() . '/equipment_usage_report?from=20200101&to=20220303'
        );
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), $response->getContent());
    }

    public function testGetUsageReportNonexistentStation()
    {
        $client = self::$kernel->getContainer()->get('test.client');
        $client->request('GET', '/stations/534234/equipment_usage_report?from=20200101&to=20220303');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode(), $response->getContent());
    }

    public function testPostNewOrderValidationFailure()
    {
        $client = self::$kernel->getContainer()->get('test.client');
        $client->request(
            'POST',
            '/orders',
            [
                'startStation'           => $this->berlin->getId(),
                'endStation'             => $this->frankfurt->getId(),
                'startDayCode'           => '20220322',
                'endDayCode'             => '20220323',
                'orderEquipmentCounters' => [
                    [
                        'equipmentType' => $this->toothBrush->getId(),
                        'count'         => 1,
                    ],
                    [
                        'equipmentType' => $this->towel->getId(),
                        'count'         => 1,
                    ],
                ],
            ]
        );

        /** @var Response $response */
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), $response->getContent());

        $bodyArr         = json_decode($response->getContent(), true);
        $expectedBodyArr = [
            0 => 'root:Placing this order would make station "Berlin" run out of equipment "Tooth brush" on day 20220322!',
            1 => 'root:Placing this order would make station "Berlin" run out of equipment "Towel" on day 20220322!',
        ];

        $this->assertEquals($expectedBodyArr, $bodyArr);
    }


    public function testPostNewOrderSuccess()
    {
        $counterGridService = self::getContainer()->get(CounterGridService::class);
        $counterGridService->extendCounterGrid(100);
        $counterGridService->applyEquipmentShipment($this->berlin, $this->toothBrush, '20220322', 2);
        $counterGridService->applyEquipmentShipment($this->berlin, $this->towel, '20220322', 2);

        $counterGridService->getEntityManager()->clear();

        $this->assertEquals(0, $this->getEntityCount(Order::class));
        $this->assertEquals(0, $this->getEntityCount(OrderEquipmentCounter::class));

        $client = self::$kernel->getContainer()->get('test.client');

        $postRequestBody = [
            'startStation'           => $this->berlin->getId(),
            'endStation'             => $this->frankfurt->getId(),
            'startDayCode'           => '20220322',
            'endDayCode'             => '20220323',
            'orderEquipmentCounters' => [
                [
                    'equipmentType' => $this->toothBrush->getId(),
                    'count'         => 1,
                ],
                [
                    'equipmentType' => $this->towel->getId(),
                    'count'         => 1,
                ],
            ],
        ];

        $client->request(
            'POST',
            '/orders',
            $postRequestBody
        );

        /** @var Response $response */
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), $response->getContent());

        $bodyArr = json_decode($response->getContent(), true);
        $this->assertEquals([], $bodyArr);

        $this->assertEquals(1, $this->getEntityCount(Order::class));
        $this->assertEquals(2, $this->getEntityCount(OrderEquipmentCounter::class));

    }

}