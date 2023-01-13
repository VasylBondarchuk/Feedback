<?php

namespace Training\Feedback\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Model\Feedback as FeedbackModel;

/**
 * Feedback resource model
 */
class Feedback extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('training_feedback', 'feedback_id');
    }

    /**
     * @return int
     */
    public function getAllFeedbackNumber(): int
    {
        $adapter = $this->getConnection();

        $select = $adapter
            ->select()
            ->from('training_feedback', new \Zend_Db_Expr('COUNT(*)'));

        return (int)$adapter->fetchOne($select);
    }

    /**
     * @return int
     */
    public function getActiveFeedbackNumber(): int
    {
        $adapter = $this->getConnection();

        $select = $adapter
            ->select()
            ->from('training_feedback', new \Zend_Db_Expr('COUNT(*)'))
            ->where(FeedbackInterface::IS_ACTIVE . ' = ?', FeedbackModel::STATUS_ACTIVE_VALUE);
        return (int)$adapter->fetchOne($select);
    }

    /**
     * @return int
     */
    public function getNotPublishedFeedbacksNumber(): int
    {
        return $this->getAllFeedbackNumber() - $this->getActiveFeedbackNumber();
    }

    /**
     * @return int
     */
    public function getNotRepliedFeedbacksNumber(): int
    {
        $adapter = $this->getConnection();

        $select = $adapter
            ->select()
            ->from('training_feedback', new \Zend_Db_Expr('COUNT(*)'))
            ->where(FeedbackInterface::REPLY_NOTIFICATION . ' = ?', FeedbackModel::REPLY_NOTIFY)
            ->where(FeedbackInterface::IS_REPLIED . ' = ?', FeedbackModel::IS_NOT_REPLIED_VALUE);
        return (int)$adapter->fetchOne($select);
    }
}


