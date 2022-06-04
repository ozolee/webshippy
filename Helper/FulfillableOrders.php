<?php

use JetBrains\PhpStorm\Pure;

include('Repository\Orders.php');

class FulfillableOrders
{

    private array $header_labels;
    private array $orders;
    private Orders $repository;

    public function __construct()
    {
        $this->repository = new Orders();
        $this->readOrders();
    }

    private function readOrders()
    {
        list($this->orders, $this->header_labels) = $this->repository->readOrdersFromCsv();
        $this->orderSorting();
    }

    private function orderSorting()
    {
        usort( $this->orders, function (Order $a, Order $b) {
            $pc = -1 * ($a->getPriority() <=> $b->getPriority());
            return $pc == 0 ? $a->getCreatedAt() <=> $b->getCreatedAt() : $pc;
        });
    }

    public function getHeaderRow(): string
    {
        $header_first_row = "";
        $header_second_row = "";
        foreach ($this->header_labels as $label) {
            $header_first_row .= str_pad($label, 20);
            $header_second_row .= str_repeat('=', 20);;
        }
        return $header_first_row . "\n" . $header_second_row . "\n";
    }

    /**
     * @param $stock
     * @return string
     */
    #[Pure] public function getBodyRow($stock): string
    {
        $table_body = "";
        /** @var Order $order */
        foreach ($this->orders as $order) {
            if ($stock->{$order->getProductId()} >= $order->getQuantity()) {
                foreach ($this->header_labels as $label) {
                    $table_body .= match ($label) {
                        'product_id' => str_pad($order->getProductId(), 20),
                        'quantity' => str_pad($order->getQuantity(), 20),
                        'priority' => match ($order->getPriority()) {
                            1 => str_pad('low', 20),
                            2 => str_pad('medium', 20),
                            3 => str_pad('high', 20),
                        },
                        'created_at' => str_pad($order->getCreatedAt(), 20),
                    };
                }
                $table_body .= "\n";
            }
        }
        return $table_body;
    }
}