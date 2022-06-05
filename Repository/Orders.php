<?php

include ('..\Entity\Order.php');

class Orders
{
    public const RESOURCE_PATH = "..\Resource";

    public function readOrdersFromCsv(): array
    {
        $orders = array();
        $headerLabels = array();
        $rowCounter = 1;
        if (($file = fopen(Orders::RESOURCE_PATH . '\Orders.csv', 'r')) !== false) {
            while (($data = fgetcsv($file)) !== false) {
                if ($rowCounter == 1) {
                    $headerLabels = $data;
                } else {
                    $order = new Order();
                    $order->setProductId($data[0]);
                    $order->setQuantity($data[1]);
                    $order->setPriority($data[2]);
                    $order->setCreatedAt($data[3]);
                    $orders[] = $order;
                }
                $rowCounter++;
            }
            fclose($file);
        }
        return array($orders, $headerLabels);
    }
}