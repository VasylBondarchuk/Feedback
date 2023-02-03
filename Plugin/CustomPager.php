<?php
declare(strict_types=1);

namespace Training\Feedback\Plugin;

use Magento\Theme\Block\Html\Pager;

class CustomPager
{
    public function afterGetAvailableLimit(Pager $subject): array
    {
        return [5 => 5, 10 => 10, 20 => 20, 50 => 50];
    }
}
