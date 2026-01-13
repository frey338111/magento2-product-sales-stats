<?php
declare(strict_types=1);

namespace Hmh\ProductSalesStats\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    private const XML_PATH_ENABLED = 'hmh_productsalesstats/general/enabled';
    private const XML_PATH_PERIOD = 'hmh_productsalesstats/general/period';
    private const XML_PATH_THRESHOLD = 'hmh_productsalesstats/general/threshold';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getPeriod(?int $storeId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_PERIOD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getThreshold(?int $storeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_THRESHOLD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
