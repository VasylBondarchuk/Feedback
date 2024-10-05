<?php

namespace Training\Feedback\Model\Source;

use Magento\Store\Model\System\Store;

class CustomStoreViews extends \Magento\Store\Model\System\Store
{
    public function toOptionArray($withGroups = false, $defaultValues = false)
    {
        $options = parent::getStoreValuesForForm(false, true);
        // Remove the 'All Store Views' option (store_id = 0)
        unset($options[0]);
        return $options;
    }
}
