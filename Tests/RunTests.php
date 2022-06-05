<?php

include_once ('OrdersTest.php');
include_once ('FulfillableOrdersTest.php');

try {
    $ordersTest = new OrdersTest();
    $fulfillableOrdersTest = new FulfillableOrdersTest();
    echo $ordersTest->readOrdersFromCsvTest();
    echo $fulfillableOrdersTest->sortOrdersTest();
    echo $fulfillableOrdersTest->getTableHeaderTest();
    echo $fulfillableOrdersTest->getOrdersBodyTest();
    echo $fulfillableOrdersTest->matchLabelTest();
    echo $fulfillableOrdersTest->matchPriorityLevelTest();
} catch (Exception $e) {
    echo "Test error message:" . $e->getMessage();
}
