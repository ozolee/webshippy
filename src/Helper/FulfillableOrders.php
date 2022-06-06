<?php

namespace src\Helper;

use src\Entity\Order;
use src\Repository\Orders;

class FulfillableOrders
{
    private Orders $repository;

    public function __construct()
    {
        $this->repository = new Orders();
    }

    public function readTableData(): array
    {
        return $this->repository->readOrdersFromCsv();
    }

    public function sortOrders(array $orders): array
    {
        usort( $orders, function (Order $a, Order $b) {
            $pc = -1 * ($a->getPriority() <=> $b->getPriority());
            return $pc == 0 ? $a->getCreatedAt() <=> $b->getCreatedAt() : $pc;
        });
        return $orders;
    }

    public function getTableHeader(array $headerLabels): string
    {
        $headerFirstRow = "";
        $headerSecondRow = "";
        foreach ($headerLabels as $label) {
            $headerFirstRow .= str_pad($label, 20);
            $headerSecondRow .= str_repeat('=', 20);;
        }
        return $headerFirstRow . "\n" . $headerSecondRow . "\n";
    }

    /**
     * @param $stock
     * @param array $orders
     * @param array $headerLabels
     * @return string
     */
    public function getOrdersBody($stock, array $orders, array $headerLabels): string
    {
        $tableBody = "";
        /** @var Order $order */
        foreach ($orders as $order) {
            if ($stock->{$order->getProductId()} >= $order->getQuantity()) {
                $tableBody .= $this->getTableBody($order, $headerLabels);
            }
        }
        return $tableBody;
    }

    /**
     * @param Order $order
     * @param array $headerLabels
     * @return string
     */
    private function getTableBody(Order $order, array $headerLabels): string
    {
        $tableBody = "";
        foreach ($headerLabels as $label) {
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
    public function matchLabel($label, Order $order): string
    {
        return match ($label) {
            'product_id' => $order->getProductId(),
            'quantity' => $order->getQuantity(),
            'priority' => $this->matchPriorityLevel($order->getPriority()),
            'created_at' => $order->getCreatedAt(),
        };
    }

    /**
     * @param int $priority
     * @return string
     */
    public function matchPriorityLevel(int $priority): string
    {
        return match ($priority) {
            1 => 'low',
            2 => 'medium',
            3 => 'high',
        };
    }
}