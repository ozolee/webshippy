<?php

use JetBrains\PhpStorm\Pure;

include('..\Repository\Orders.php');

class FulfillableOrders
{

    private array $headerLabels;
    private array $orders;
    private Orders $repository;

    public function __construct()
    {
        $this->repository = new Orders();
        $this->readOrders();
    }

    private function readOrders()
    {
        list($this->orders, $this->headerLabels) = $this->repository->readOrdersFromCsv();
        $this->orderSorting();
    }

    private function orderSorting()
    {
        usort( $this->orders, function (Order $a, Order $b) {
            $pc = -1 * ($a->getPriority() <=> $b->getPriority());
            return $pc == 0 ? $a->getCreatedAt() <=> $b->getCreatedAt() : $pc;
        });
    }

    public function getTableHeader(): string
    {
        $headerFirstRow = "";
        $headerSecondRow = "";
        foreach ($this->headerLabels as $label) {
            $headerFirstRow .= str_pad($label, 20);
            $headerSecondRow .= str_repeat('=', 20);;
        }
        return $headerFirstRow . "\n" . $headerSecondRow . "\n";
    }

    /**
     * @param $stock
     * @return string
     */
    #[Pure] public function getOrdersBody($stock): string
    {
        $tableBody = "";
        /** @var Order $order */
        foreach ($this->orders as $order) {
            if ($stock->{$order->getProductId()} >= $order->getQuantity()) {
                $tableBody .= $this->getTableBody($order);
            }
        }
        return $tableBody;
    }

    /**
     * @param Order $order
     * @return string
     */
    #[Pure] private function getTableBody(Order $order): string
    {
        $tableBody = "";
        foreach ($this->headerLabels as $label) {
            $tableBody .= str_pad($this->matchLabel($label, $order), 20);
        }
        $tableBody .= "\n";
        return $tableBody;
    }

    /**
     * @param $label
     * @param Order $order
     * @return string
     */
    #[Pure] private function matchLabel($label, Order $order): string
    {
        return match ($label) {
            'product_id' => $order->getProductId(),
            'quantity' => $order->getQuantity(),
            'priority' => $this->matchPriorityLevel($order->getPriority()),
            'created_at' => $order->getCreatedAt(),
        };
    }

    /**
     * @param $priority
     * @return string
     */
    private function matchPriorityLevel($priority): string
    {
        return match ($priority) {
            1 => 'low',
            2 => 'medium',
            3 => 'high',
        };
    }
}