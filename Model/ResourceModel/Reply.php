<?php

namespace Training\Feedback\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Reply resource model
 */
class Reply extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('training_feedback_reply', 'reply_id');
    }
}
