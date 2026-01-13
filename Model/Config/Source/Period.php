<?php
declare(strict_types=1);

namespace Hmh\ProductSalesStats\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Period implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'day', 'label' => __('Day')],
            ['value' => 'week', 'label' => __('Week')],
            ['value' => 'month', 'label' => __('Month')],
        ];
    }
}
