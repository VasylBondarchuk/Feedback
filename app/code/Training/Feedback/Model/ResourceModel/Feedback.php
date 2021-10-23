<?php

namespace Training\Feedback\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Feedback extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('training_feedback', 'feedback_id');
    }

    public function getAllFeedbackNumber()
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()
            ->from('training_feedback', new \Zend_Db_Expr('COUNT(*)'));

        return $adapter->fetchOne($select);
    }

    public function getActiveFeedbackNumber()
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()
            ->from('training_feedback', new \Zend_Db_Expr('COUNT(*)'))
            ->where('is_active = ?', \Training\Feedback\Model\Feedback::STATUS_ACTIVE);
        return $adapter->fetchOne($select);
    }
}
