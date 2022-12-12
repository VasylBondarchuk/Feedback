<?php

namespace Training\Feedback\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 *
 */
interface FeedbackInterface extends ExtensibleDataInterface
{
    /**#@+*/
    const FEEDBACK_ID = 'feedback_id';
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
    /**#@-*/
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();
    /**
     * Get author name
     *
     * @return string
     */
    public function getAuthorName(): string;
    /**
     * Get author email
     *
     * @return string
     */
    public function getAuthorEmail(): string;
    /**
     * Get message
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return string
     */
    public function getReplyNotification(): string;
    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();
    /**
     * Get update time
     *
    © 2018 M2Training.com.ua 1
     * @return string|null
     */
    public function getUpdateTime();
    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();
    /**
     * Set ID
     *
     * @param int $id
     * @return FeedbackInterface
     */
    public function setId($id);
    /**
     * Set author name
     *
     * @param string $authorName
     * @return FeedbackInterface
     */
    public function setAuthorName($authorName);
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
     * @param bool|int $isActive
     * @return FeedbackInterface
     */
    public function setIsActive($isActive): FeedbackInterface;

}
