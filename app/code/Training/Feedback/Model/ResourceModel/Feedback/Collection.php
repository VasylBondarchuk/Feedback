<?php

namespace Training\Feedback\Model\ResourceModel\Feedback;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Training\Feedback\Model\Feedback::class,
            \Training\Feedback\Model\ResourceModel\Feedback::class
        );
    }
}
