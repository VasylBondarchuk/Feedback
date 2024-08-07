<?php

namespace Training\Feedback\Api\Data\Feedback;

/**
 *
 */
interface FeedbackInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const FEEDBACK_ID = 'feedback_id';

    /**
     *
     */
    const STORE_ID = 'store_id';

    /**
     *
     */
    const CUSTOMER_ID = 'customer_id';
    /**
     *
     */
    const AUTHOR_NAME = 'author_name';
    /**
     *
     */
    const AUTHOR_EMAIL = 'author_email';
    /**
     *
     */
    const MESSAGE = 'message';

    /**
     *
     */
    const REPLY_NOTIFICATION = 'reply_notification';
    /**
     *
     */
    const CREATION_TIME = 'creation_time';
    /**
     *
     */
    const UPDATE_TIME = 'update_time';
    /**
     *
     */
    const IS_ACTIVE = 'is_active';

    /**
     *
     */
    const IS_REPLIED = 'is_replied';
    
    /**
     *
     */
    const IS_ANONYMOUS = 'is_anonymous';

    /**
     * Get FEEDBACK_ID
     *
     * @return int|null
     */
    public function getFeedbackId(): ?int;

    /**
     * @return int|null
     */
    public function getStoreId(): ?int;

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int;
    /**
     * Get author name
     *
     * @return string
     */
    public function getAuthorName(): string;
    /**
     * Get author email
     *
     * @return string|null
     */
    public function getAuthorEmail(): ?string;
    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage(): ?string;
    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime(): ?string;
    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime(): ?string;
    /**
     * Is active
     *
     * @return bool|null
     */
    public function getIsActive(): ?bool;

    /**
     * @return mixed
     */
    public function getIsReplied();
    
    /**
     * @return string
     */
    public function getIsAnonymous();
    

    /**
     * Set ID
     *
     * @param int $feedbackId
     * @return FeedbackInterface
     */
    public function setFeedbackId(int $feedbackId): FeedbackInterface;


    /**
     * @param int $storeId
     * @return FeedbackInterface
     */
    public function setStoreId(int $storeId): FeedbackInterface;

    /**
     * @param int $customerId
     * @return FeedbackInterface
     */
    public function setCustomerId(int $customerId): FeedbackInterface;

    /**
     * Set author name
     *
     * @param string $authorName
     * @return FeedbackInterface
     */
    public function setAuthorName(string $authorName): FeedbackInterface;
    /**
     * Set author email
     *
     * @param string $authorEmail
     * @return FeedbackInterface
     */
    public function setAuthorEmail(string $authorEmail): FeedbackInterface;
    /**
     * Set message
     *
     * @param string $message
     * @return FeedbackInterface
     */
    public function setMessage(string $message): FeedbackInterface;
    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return FeedbackInterface
     */
    public function setCreationTime(string $creationTime): FeedbackInterface;
    /**
     * Set update time
     *
     * @param string $updateTime
     * @return FeedbackInterface
     */
    public function setUpdateTime(string $updateTime): FeedbackInterface;

    /**
     * Set is active
     *
     * @param int $isActive
     * @return FeedbackInterface
     */
    public function setIsActive(int $isActive): FeedbackInterface;

    /**
     * @param int $isReplied
     * @return FeedbackInterface
     */
    public function setIsReplied(int $isReplied): FeedbackInterface;
    
    public function setIsAnonymous(int $isReplied): FeedbackInterface;
}

