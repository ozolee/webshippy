<?php
declare(strict_types=1);

namespace src\Tests;

use PHPUnit\Framework\TestCase;
use src\Repository\Orders;

class OrdersTest extends TestCase
{
    /**
     * @test
     * @dataProvider orderReaderProvider
     * @param int $orderExpectedCounter
     * @param int $headerExpectedCounter
     */
    public function readOrdersFromCsvTest(int $orderExpectedCounter, int $headerExpectedCounter)
    {
        $repository = new Orders();

        list($orders, $headerLabels) = $repository->readOrdersFromCsv();

        $this->assertCount($orderExpectedCounter, $orders);
        $this->assertCount($headerExpectedCounter, $headerLabels);
    }

    public function orderReaderProvider(): array
    {
        return array(
            array(10, 4)
        );
    }
}