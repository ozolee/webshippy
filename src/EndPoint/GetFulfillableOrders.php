<?php

require_once '..\..\vendor\autoload.php';

use src\Helper\FulfillableOrders;

if (!isset($argc) || $argc != 2) {
    echo 'Ambiguous number of parameters!';
    exit;
}

if (($stock = json_decode($argv[1])) == null) {
    echo 'Invalid json!';
    exit;
}

try {
    $helper = new FulfillableOrders();
    list($orders, $headerLabels) = $helper->readTableData();
    $orders = $helper->sortOrders($orders);
    echo $helper->getTableHeader($headerLabels);
    echo $helper->getOrdersBody($stock, $orders, $headerLabels);
} catch (Exception $e) {
    echo "Error message: " . $e->getMessage();
    exit;
}
