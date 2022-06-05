<?php

include_once('..\Repository\Orders.php');

class OrdersTest extends Orders
{
    public function readOrdersFromCsvTest(): string
    {
        $response = "";
        list($orders, $headerLabels) = $this->readOrdersFromCsv();
        if (!is_array($orders) || (count($orders) != 10)) {$response .= str_pad("Failed", 20) . "Invalid order data from CSV. \n";}
        if (!is_array($headerLabels) || (count($headerLabels) != 4)) {$response .= str_pad("Failed", 20) . "Invalid header data from CSV. \n";}
        ($response != "") ? $response .= str_pad("Attention", 20) . "Wrong data from CSV. \n" : $response .= str_pad("Success", 20) . "Exact data from CSV. \n";
        return $response;
    }
}