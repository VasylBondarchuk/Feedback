<?php

namespace Training\Feedback\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Training\Feedback\Api\Data\Reply\ReplyInterface;

/**
 * Reply model
 */
class Reply extends AbstractExtensibleModel implements ReplyInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Reply::class);
    }

    /**
     * @return mixed|null
     */
    public function getReplyId()
    {
        return $this->getData(self::REPLY_ID);
    }

    /**
     * @return mixed
     */
    public function getFeedbackId() : int
    {
        return (int)$this->getData(self::FEEDBACK_ID);
    }

    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->getData(self::ADMIN_ID);
    }

    /**
     * @return mixed
     */
    public function getReplyText()
    {
        return $this->getData(self::REPLY_TEXT);
    }

    /**
     * @return mixed
     */
    public function getReplyCreationTime()
    {
        return $this->getData(self::REPLY_CREATION_TIME);
    }

    /**
     * @return mixed
     */
    public function getReplyUpdateTime()
    {
        return $this->getData(self::REPLY_UPDATE_TIME);
    }

    /**
     * @param int $replyId
     * @return ReplyInterface
     */
    public function setReplyId(int $replyId) : ReplyInterface
    {
        return $this->setData(self::REPLY_ID, $replyId);
    }

    /**
     * @param int $feedbackId
     * @return ReplyInterface
     */
    public function setFeedbackId(int $feedbackId) : ReplyInterface
    {
        return $this->setData(self::FEEDBACK_ID, $feedbackId);
    }

    /**
     * @return mixed
     */
    public function setAdminId(int $adminId) : ReplyInterface
    {
        return $this->setData(self::ADMIN_ID, $adminId);
    }

    /**
     * @param string|null $replyText
     * @return ReplyInterface
     */
    public function setReplyText(?string $replyText) : ReplyInterface
    {
        return $this->setData(self::REPLY_TEXT, $replyText);
    }

    /**
     * @param string $replyCreationTime
     * @return ReplyInterface
     */
    public function setReplyCreationTime(string $replyCreationTime) : ReplyInterface
    {
        return $this->setData(self::REPLY_CREATION_TIME, $replyCreationTime);
    }

    /**
     * @return mixed
     */
    public function setReplyUpdateTime(string $replyUpdateTime) : ReplyInterface
    {
        return $this->setData(self::REPLY_UPDATE_TIME, $replyUpdateTime);
    }

}
