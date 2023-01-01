<?php
declare(strict_types=1);

namespace Training\Feedback\Block;

use Magento\Theme\Block\Html\Pager;
/**
 *
 */
class CustomPager extends Pager
{
    /**
     * Retrieve pager limit
     *
     * @return array
     */
    public function getAvailableLimit(): array
    {
        return [5 => 5, 10 => 10, 20 => 20, 50 => 50];
    }
}


