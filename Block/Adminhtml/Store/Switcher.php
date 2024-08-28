<?php

namespace Training\Feedback\Block\Adminhtml\Store;

use Magento\Backend\Block\Store\Switcher as MagentoSwitcher;

class Switcher extends MagentoSwitcher
{
    public function getStoreValuesForForm($useDefault = false, $showAll = true)
    {
        $options = parent::getStoreValuesForForm($useDefault, $showAll);
        // Filter out the 'All Store Views' option
        return array_filter($options, function ($option) {
            return $option['value'] !== '0'; // '0' typically represents 'All Store Views'
        });
    }
}
