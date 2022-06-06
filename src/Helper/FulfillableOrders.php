<?php

namespace src\Helper;

use src\Entity\Order;
use src\Repository\Orders;

class FulfillableOrders
{

    protected array $headerLabels;
    protected array $orders;
    private Orders $repository;

    public function __construct()
    {
        $this->repository = new Orders();
    }

    public function readTableData()
    {
        list($this->orders, $this->headerLabels) = $this->repository->readOrdersFromCsv();
    }

    public function sortOrders()
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
    public function getOrdersBody($stock): string
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
    private function getTableBody(Order $order): string
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
    protected function matchLabel($label, Order $order): string
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
    protected function matchPriorityLevel($priority): string
    {
        return match ($priority) {
            1 => 'low',
            2 => 'medium',
            3 => 'high',
        };
    }
}