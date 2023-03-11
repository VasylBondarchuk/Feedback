<?php
declare(strict_types=1);

namespace Training\Feedback\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;

/**
 * Feedback model
 */
class Feedback extends AbstractExtensibleModel implements FeedbackInterface
{
    /**
     *
     */
    const STATUS_ACTIVE_VALUE = 1;
    /**
     *
     */
    const STATUS_INACTIVE_VALUE = 0;
    /**
     *
     */
    const STATUS_ACTIVE_LABEL = 'Published';
    /**
     *
     */
    const STATUS_INACTIVE_LABEL = 'Not published';
    /**
     *
     */
    const REPLY_NOTIFY = 1;
    /**
     *
     */
    const REPLY_DO_NOT_NOTIFY = 0;
    /**
     *
     */
    const REPLY_NOTIFY_LABEL = 'Yes';
    /**
     *
     */
    const REPLY_DO_NOT_NOTIFY_LABEL = 'No';

    /**
     *
     */
    const IS_REPLIED_VALUE = 1;
    /**
     *
     */
    const IS_NOT_REPLIED_VALUE = 0;
    /**
     *
     */
    const IS_REPLIED_LABEL = 'Yes';
    /**
     *
     */
    const IS_NOT_REPLIED_LABEL = 'No';

    /**
     * @var string
     */
    protected $_eventPrefix = 'training_feedback';
    /**
     * @var string
     */
    protected $_eventObject = 'feedback';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Feedback::class);
    }

    /**
     * Retrieve Get FEEDBACK_ID
     *
     * @return int
     */
    public function getFeedbackId(): int
    {
        return (int)$this->getData(self::FEEDBACK_ID);
    }

    public function getStoreId(): ?int
    {
        return (int)$this->getData(self::STORE_ID);
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * Get author name
     *
     * @return string
     */
    public function getAuthorName(): string
    {
        return (string)$this->getData(self::AUTHOR_NAME);
    }
    /**
     * Get author email
     *
     * @return string
     */
    public function getAuthorEmail(): string
    {
        return $this->getData(self::AUTHOR_EMAIL);
    }
    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getReplyNotification(): string
    {
        return $this->getData(self::REPLY_NOTIFICATION);
    }

    /**
     * Retrieve post creation time
     *
     * @return string
     */
    public function getCreationTime(): string
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Retrieve post update time
     *
     * @return string
     */
    public function getUpdateTime(): string
    {
        return $this->getData(self::UPDATE_TIME);
    }
    /**
     * Is active
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function getIsReplied()
    {
        return $this->getData(self::IS_REPLIED);
    }

    /**
     * @return string
     */
    public function getIsPublished() : string
    {
        return $this->getIsActive()
            ? self::STATUS_ACTIVE_LABEL
            : self::STATUS_INACTIVE_LABEL;
    }
    /**
     * Set ID
     *
     * @param int $feedbackId
     * @return Feedback Interface
     */
    public function setFeedbackId(int $feedbackId): FeedbackInterface
    {
        return $this->setData(self::FEEDBACK_ID, $feedbackId);
    }

    public function setStoreId(int $storeId): FeedbackInterface
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @param int $customerId
     * @return FeedbackInterface
     */
    public function setCustomerId(int $customerId): FeedbackInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Set author name
     *
     * @param string $authorName
     * @return Feedback Interface
     */
    public function setAuthorName(string $authorName): FeedbackInterface
    {
        return $this->setData(self::AUTHOR_NAME, $authorName);
    }
    /**
     * Set author email
     *
     * @param string $authorEmail
     * @return Feedback Interface
     */
    public function setAuthorEmail(string $authorEmail): FeedbackInterface
    {
        return $this->setData(self::AUTHOR_EMAIL, $authorEmail);
    }
    /**
     * Set message
     *
     * @param string $message
     * @return Feedback Interface
     */
    public function setMessage(string $message): FeedbackInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @param string $replyNotification
     * @return FeedbackInterface
     */
    public function setReplyNotification(int $replyNotification): FeedbackInterface
    {
        return $this->setData(self::REPLY_NOTIFICATION, $replyNotification);
    }
    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return Feedback Interface
     */
    public function setCreationTime(string $creationTime): FeedbackInterface
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return Feedback Interface
     */
    public function setUpdateTime(string $updateTime): FeedbackInterface
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return Feedback Interface
     */
    public function setIsActive($isActive): FeedbackInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function setIsReplied($isReplied): FeedbackInterface
    {
        return $this->setData(self::IS_REPLIED, $isReplied);
    }
}
