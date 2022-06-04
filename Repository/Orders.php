<?php

include ('Entity\Order.php');

class Orders
{
    public function readOrdersFromCsv()
    {
        $orders = array();
        $header_labels = array();
        $row_counter = 1;
        if (($file = fopen('orders.csv', 'r')) !== false) {
            while (($data = fgetcsv($file)) !== false) {
                if ($row_counter == 1) {
                    $header_labels = $data;
                } else {
                    $order = new Order();
                    $order->setProductId($data[0]);
                    $order->setQuantity($data[1]);
                    $order->setPriority($data[2]);
                    $order->setCreatedAt($data[3]);
                    $orders[] = $order;
                }
                $row_counter++;
            }
            fclose($file);
        }
        return array($orders, $header_labels);
    }

}