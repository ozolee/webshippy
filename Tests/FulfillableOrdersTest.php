<?php

include_once('..\Helper\FulfillableOrders.php');
include_once('..\Entity\Order.php');

class FulfillableOrdersTest extends FulfillableOrders
{
    public function sortOrdersTest(): string
    {
        $response = $this->sortOrdersTestSortingCase();
        $response .= $this->sortOrdersTestNotSortingCase();

        return $this->getTestSummaryResponse($response, "Wrong order sorting.", "Exact order sorting.");
    }

    private function sortOrdersTestSortingCase(): string
    {
        $response = "";
        $this->orders = [
            0 => $this->initOrder(1, 3,1, "2022-05-25 15:50:47"),
            1 => $this->initOrder(3, 2,3, "2022-05-20 10:30:32"),
            2 => $this->initOrder(2, 1,2, "2022-05-23 09:19:21")
        ];

        $this->sortOrders();

        if ($this->orders[0]->getProductId() !== 3) {$response .= str_pad("Failed", 20) . "Invalid order sorting. \n";}
        if ($this->orders[1]->getProductId() !== 2) {$response .= str_pad("Failed", 20) . "Invalid order sorting. \n";}
        if ($this->orders[2]->getProductId() !== 1) {$response .= str_pad("Failed", 20) . "Invalid order sorting. \n";}

        return $response;
    }

    private function sortOrdersTestNotSortingCase(): string
    {
        $response = "";
        $this->orders = [
            0 => $this->initOrder(3, 2,3, "2022-05-20 10:30:32"),
            1 => $this->initOrder(1, 3,1, "2022-05-25 15:50:47")
        ];

        $this->sortOrders();

        if ($this->orders[0]->getProductId() !== 3) {$response .= str_pad("Failed", 20) . "Invalid order not sorting. \n";}
        if ($this->orders[1]->getProductId() !== 1) {$response .= str_pad("Failed", 20) . "Invalid order not sorting. \n";}

        return $response;
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

    public function getTableHeaderTest(): string
    {
        $response = $this->getTableHeaderTestFilledCase();
        $response .= $this->getTableHeaderTestEmptyCase();

        return $this->getTestSummaryResponse($response, "Wrong table header creation.", "Exact table header creation.");
    }

    private function getTableHeaderTestFilledCase(): string
    {
        $response = "";
        $expectedFilledTableHeader = "product_id          quantity            priority            created_at          \n================================================================================\n";

        $this->headerLabels = array("product_id", "quantity", "priority", "created_at");
        $tableHeader = $this->getTableHeader();
        if (strcmp($tableHeader, $expectedFilledTableHeader)) {$response .= str_pad("Failed", 20) . "Invalid filled table header creation. \n";}

        return $response;
    }

    private function getTableHeaderTestEmptyCase(): string
    {
        $response = "";
        $expectedEmptyTableHeader = "\n\n";
        $this->headerLabels = array();
        $tableHeader = $this->getTableHeader();
        if (strcmp($tableHeader, $expectedEmptyTableHeader)) {$response .= str_pad("Failed", 20) . "Invalid empty table header creation. \n";}

        return $response;
    }

    public function getOrdersBodyTest(): string
    {
        $response = $this->getOrdersBodyTestFilledCase();
        $response .= $this->getOrdersBodyTestEmptyCase();

        return $this->getTestSummaryResponse($response, "Wrong table body creation.", "Exact table body creation.");
    }

    private function getOrdersBodyTestFilledCase(): string
    {
        $response = "";
        $expectedFilledContent = "1                   2                   high                2021-03-25 14:51:47 \n2                   1                   medium              2021-03-21 14:00:26 \n3                   1                   medium              2021-03-22 12:31:54 \n1                   1                   low                 2021-03-25 19:08:22 \n";
        $stock = json_decode('{"1":2,"2":1,"3":1}');
        $this->readTableData();
        $this->sortOrders();
        $filledTableBodyContent = $this->getOrdersBody($stock);
        if (strcmp($filledTableBodyContent, $expectedFilledContent)) {$response .= str_pad("Failed", 20) . "Invalid filled table body creation. \n";}

        return$response;
    }

    private function getOrdersBodyTestEmptyCase(): string
    {
        $response = "";
        $expectedEmptyContent = "";
        $stock = json_decode('{"1":0,"2":0,"3":0}');
        $this->readTableData();
        $this->sortOrders();
        $emptyTableBodyContent = $this->getOrdersBody($stock);
        if (strcmp($emptyTableBodyContent, $expectedEmptyContent)) {$response .= str_pad("Failed", 20) . "Invalid empty table body creation. \n";}

        return$response;
    }

    public function matchLabelTest(): string
    {
        $response = "";
        $result = array(
            0 => $this->matchLabel("created_at", $this->initOrder(1, 3,1, "2022-05-25 15:50:47")),
            1 => $this->matchLabel("priority", $this->initOrder(2, 1,1, "2022-05-22 11:34:32")),
            2 => $this->matchLabel("priority", $this->initOrder(2, 4,3, "2022-05-24 14:45:47"))
        );

        if (strcmp($result[0], "2022-05-25 15:50:47")) {$response .= str_pad("Failed", 20) . "Invalid created_at label matching. \n";}
        if (strcmp($result[1], "low")) {$response .= str_pad("Failed", 20) . "Invalid low priority label matching. \n";}
        if (strcmp($result[2], "high")) {$response .= str_pad("Failed", 20) . "Invalid high priority label matching. \n";}

        return $this->getTestSummaryResponse($response, "Wrong label matching.", "Exact label matching.");
    }

    public function matchPriorityLevelTest(): string
    {
        $response = "";
        $result = array(
            0 => $this->matchPriorityLevel(1),
            1 => $this->matchPriorityLevel(2),
            2 => $this->matchPriorityLevel(3),
        );

        if (strcmp($result[0], "low")) {$response .= str_pad("Failed", 20) . "Invalid low priority matching. \n";}
        if (strcmp($result[1], "medium")) {$response .= str_pad("Failed", 20) . "Invalid medium priority matching. \n";}
        if (strcmp($result[2], "high")) {$response .= str_pad("Failed", 20) . "Invalid high priority matching. \n";}

        return $this->getTestSummaryResponse($response, "Wrong priority matching.", "Exact priority matching.");
    }

    private function getTestSummaryResponse(string $response,string $attentionMessage,string $successMessage): string
    {
        ($response != "") ? $response .= str_pad("Attention", 20) . $attentionMessage ." \n" : $response .= str_pad("Success", 20) . $successMessage . " \n";
        return $response;
    }
}