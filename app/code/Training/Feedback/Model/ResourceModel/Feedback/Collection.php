<?php

namespace Training\Feedback\Model\ResourceModel\Feedback;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\ResourceModel\Feedback as FeedbackResourceModel;

class Collection extends AbstractCollection
{

    protected $_eventPrefix = 'training_feedback_collection';
    protected $_eventObject = 'feedback_collection';

    protected function _construct()
    {
        $this->_init(
            FeedbackModel::class,
            FeedbackResourceModel::class
        );
    }
}
