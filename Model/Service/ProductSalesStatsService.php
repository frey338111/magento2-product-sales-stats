<?php
declare(strict_types=1);

namespace Hmh\ProductSalesStats\Model\Service;

use Hmh\ProductSalesStats\Model\Config\ConfigProvider;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;

class ProductSalesStatsService
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    public function getQtySold(int $productId, ?int $storeId = null): float
    {
        [$from, $to] = $this->getDateRange($this->configProvider->getPeriod($storeId));

        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
            ->from(
                ['order_item' => $this->resourceConnection->getTableName('sales_order_item')],
                ['ordered_qty' => 'SUM(order_item.qty_ordered)']
            )
            ->joinInner(
                ['order' => $this->resourceConnection->getTableName('sales_order')],
                'order.entity_id = order_item.order_id',
                []
            )
            ->where('order.state <> ?', Order::STATE_CANCELED)
            ->where('order_item.product_id = ?', $productId)
            ->where('order_item.qty_ordered > ?', 0)
            ->where('order.created_at >= ?', $from)
            ->where('order.created_at <= ?', $to);

        if ($storeId !== null) {
            $select->where('order_item.store_id = ?', $storeId);
        }

        $result = $connection->fetchOne($select);

        return $result !== null ? (float) $result : 0.0;
    }

    private function getDateRange(string $period): array
    {
        $to = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $intervalSpec = match ($period) {
            'week' => 'P1W',
            'month' => 'P1M',
            default => 'P1D',
        };

        $from = $to->sub(new \DateInterval($intervalSpec));

        return [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d H:i:s')];
    }
}
