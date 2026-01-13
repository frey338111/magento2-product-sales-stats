<?php
declare(strict_types=1);

namespace Hmh\ProductSalesStats\Plugin\Catalog\Block\Product\View;

use Hmh\ProductSalesStats\Model\Config\ConfigProvider;
use Hmh\ProductSalesStats\Model\Service\ProductSalesStatsService;
use Magento\Catalog\Block\Product\View;
use Magento\Framework\Serialize\Serializer\Json;

class GetJsonConfigPlugin
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly Json $jsonSerializer,
        private readonly ProductSalesStatsService $productSalesStatsService
    ) {
    }

    /**
     * Append product sold stats to product price JSON config.
     */
    public function afterGetJsonConfig(View $subject, $result): string
    {
        $product = $subject->getProduct();
        if (!$product) {
            return $result;
        }

        if (!$this->configProvider->isEnabled((int)$product->getStoreId())) {
            return $result;
        }
        try {
            $config = $this->jsonSerializer->unserialize($result);
        } catch (\InvalidArgumentException $exception) {
            return $result;
        }
        $qtySold = $this->productSalesStatsService->getQtySold((int)$product->getId(), (int)$product->getStoreId());
        if ($qtySold < $this->configProvider->getThreshold((int)$product->getStoreId())) {
            return $result;
        }
        $config['total_sold'] = [
            'period' => $this->configProvider->getPeriod((int)$product->getStoreId()),
            'qty'    => $qtySold,
        ];

        return $this->jsonSerializer->serialize($config);
    }
}
