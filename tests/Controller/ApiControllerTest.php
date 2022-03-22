<?php


namespace Roadsurfer\Tests\Controller;


use DateTime;
use Roadsurfer\Service\CounterGridService;
use Roadsurfer\Service\CurrentTimeProvider;
use Roadsurfer\Tests\Base\DbTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestAssertionsTrait;
use Symfony\Component\HttpFoundation\Response;

class ApiControllerTest extends DbTestCase
{
    use WebTestAssertionsTrait;

    public function testGetUsageReport()
    {
        $berlin = $this->createStation('Berlin');
        $this->createStation('Frankfurt');
        $this->createEquipmentType('Tooth brush');
        $this->createEquipmentType('Towel');

        $currentTimeProvider = static::getContainer()->get(CurrentTimeProvider::class);
        $gridService         = static::getContainer()->get(CounterGridService::class);

        $fixatedDate = DateTime::createFromFormat('Y-m-d H:i:s', '2022-03-22 06:32:33');
        $currentTimeProvider->setFixatedTime($fixatedDate);
        $gridService->extendCounterGrid(2);

        $client = self::$kernel->getContainer()->get('test.client');

        $client->request('GET', '/stations/' . $berlin->getId() . '/equipment_usage_report');

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

        $client->request('GET', '/stations/' . $berlin->getId() . '/equipment_usage_report?from=20220323&to=20220323');
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

        $client->request('GET', '/stations/' . 123123 . '/equipment_usage_report');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode(), $response->getContent());
    }

}