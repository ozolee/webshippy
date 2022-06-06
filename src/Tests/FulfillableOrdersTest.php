<?php
declare(strict_types=1);

namespace src\Tests;

use PHPUnit\Framework\TestCase;
use src\Entity\Order;
use src\Helper\FulfillableOrders;

class FulfillableOrdersTest extends TestCase
{
    /**
     * @test
     * @dataProvider orderSorterProvider
     * @param array $orders
     * @param array $expectedOrders
     */
    public function sortOrdersTest(array $orders, array $expectedOrders)
    {
        $helper = new FulfillableOrders();

        $sortedOrders = $helper->sortOrders($orders);

        $this->assertEquals($expectedOrders, $sortedOrders);
    }

    public function orderSorterProvider(): array
    {
        return array(
            array(
                array(
                    0 => $this->initOrder(1, 3,1, "2022-05-25 15:50:47"),
                    1 => $this->initOrder(3, 2,3, "2022-05-20 10:30:32"),
                    2 => $this->initOrder(2, 1,2, "2022-05-23 09:19:21")
                ),
                array(
                    0 => $this->initOrder(3, 2,3, "2022-05-20 10:30:32"),
                    1 => $this->initOrder(2, 1,2, "2022-05-23 09:19:21"),
                    2 => $this->initOrder(1, 3,1, "2022-05-25 15:50:47")
                )
            ),
            array(
                array(
                    0 => $this->initOrder(3, 2,3, "2022-05-20 10:30:32"),
                    1 => $this->initOrder(2, 1,2, "2022-05-23 09:19:21"),
                    2 => $this->initOrder(1, 3,1, "2022-05-25 15:50:47")
                ),
                array(
                    0 => $this->initOrder(3, 2,3, "2022-05-20 10:30:32"),
                    1 => $this->initOrder(2, 1,2, "2022-05-23 09:19:21"),
                    2 => $this->initOrder(1, 3,1, "2022-05-25 15:50:47")
                )
            )
        );
    }

    /**
     * @param $productId
     * @param $quantity
     * @param $priority
     * @param $createdAt
     * @return Order
     */
    private function initOrder($productId, $quantity, $priority, $createdAt): Order
    {
        $order = new Order();
        $order->setProductId($productId);
        $order->setQuantity($quantity);
        $order->setPriority($priority);
        $order->setCreatedAt($createdAt);

        return $order;
    }

    /**
     * @test
     * @dataProvider tableHeaderProvider
     * @param array $headerLabels
     * @param string $expectedHeader
     */
    public function getTableHeaderTest(array $headerLabels, string $expectedHeader)
    {
        $helper = new FulfillableOrders();

        $header = $helper->getTableHeader($headerLabels);

        $this->assertEquals($expectedHeader, $header);
    }

    public function tableHeaderProvider(): array
    {
        return array(
            array(
                array("product_id", "quantity", "priority", "created_at"),
                "product_id          quantity            priority            created_at          \n================================================================================\n"
            ),
            array(
                array(),
                "\n\n"
            )
        );
    }

    /**
     * @test
     * @dataProvider tableBodyProvider
     * @param $stock
     * @param string $expectedContent
     */
    public function getOrdersBodyTest($stock, string $expectedContent)
    {
        $helper = new FulfillableOrders();

        list($orders, $headerLabels) = $helper->readTableData();
        $orders = $helper->sortOrders($orders);
        $bodyContent = $helper->getOrdersBody($stock, $orders, $headerLabels);

        $this->assertEquals($expectedContent, $bodyContent);
    }

    public function tableBodyProvider(): array
    {
        return array(
            array(
                json_decode('{"1":2,"2":1,"3":1}'),
                "1                   2                   high                2021-03-25 14:51:47 \n2                   1                   medium              2021-03-21 14:00:26 \n3                   1                   medium              2021-03-22 12:31:54 \n1                   1                   low                 2021-03-25 19:08:22 \n"
            ),
            array(
                json_decode('{"1":0,"2":0,"3":0}'),
                ""
            )
        );
    }

    /**
     * @test
     * @dataProvider matchLabelProvider
     * @param string $label
     * @param Order $order
     * @param string $expectedLabel
     */
    public function matchLabelTest(string $label, Order $order, string $expectedLabel)
    {
        $helper = new FulfillableOrders();

        $resultLabel = $helper->matchLabel($label, $order);

        $this->assertEquals($expectedLabel, $resultLabel);
    }

    public function matchLabelProvider(): array
    {
        return array(
            array(
                "created_at",
                $this->initOrder(1, 3,1, "2022-05-25 15:50:47"),
                "2022-05-25 15:50:47"
            ),
            array(
                "priority",
                $this->initOrder(2, 1,1, "2022-05-22 11:34:32"),
                "low"
            ),
            array(
                "priority",
                $this->initOrder(2, 4,3, "2022-05-24 14:45:47"),
                "high"
            )
        );
    }

    /**
     * @test
     * @dataProvider matchPriorityProvider
     * @param int $priority
     * @param string $expectedPriority
     */
    public function matchPriorityLevelTest(int $priority, string $expectedPriority)
    {
        $helper = new FulfillableOrders();

        $resultPriority = $helper->matchPriorityLevel($priority);

        $this->assertEquals($expectedPriority, $resultPriority);
    }

    public function matchPriorityProvider(): array
    {
        return array(
            array(1, "low"),
            array(2, "medium"),
            array(3, "high")
        );
    }
}