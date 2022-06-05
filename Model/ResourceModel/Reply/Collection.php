<?php

namespace Training\Feedback\Model\ResourceModel\Reply;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Training\Feedback\Model\Reply as ReplyModel;
use Training\Feedback\Model\ResourceModel\Reply as ReplyResourceModel;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            ReplyModel::class,
            ReplyResourceModel::class
        );
    }
}
