<?php

namespace Training\Feedback\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Feedback resource model
 */
class Feedback extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('training_feedback', 'feedback_id');
    }

    /**
     * @return string
     */
    public function getAllFeedbackNumber(): string
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()
            ->from('training_feedback', new \Zend_Db_Expr('COUNT(*)'));

        return $adapter->fetchOne($select);
    }

    /**
     * @return string
     */
    public function getActiveFeedbackNumber(): string
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()
            ->from('training_feedback', new \Zend_Db_Expr('COUNT(*)'))
            ->where('is_active = ?', \Training\Feedback\Model\Feedback::STATUS_ACTIVE_VALUE);
        return $adapter->fetchOne($select);
    }
}
