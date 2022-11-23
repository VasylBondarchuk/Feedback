<?php

namespace Training\Feedback\Api\Data\Reply;

interface ReplyInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const REPLY_ID = 'reply_id';
    const FEEDBACK_ID = 'feedback_id';
    const ADMIN_ID = 'admin_id';
    const REPLY_TEXT = 'reply_text';
    const REPLY_CREATION_TIME = 'reply_creation_time';
    const REPLY_UPDATE_TIME = 'reply_update_time';

    /**
     * @return mixed|null
     */
    public function getReplyId();

    /**
     * @return mixed
     */
    public function getFeedbackId();

    /**
     * @return mixed
     */
    public function getAdminId();

    /**
     * @return mixed
     */
    public function getReplyText();

    /**
     * @return mixed
     */
    public function getReplyCreationTime();

    /**
     * @return mixed
     */
    public function getReplyUpdateTime();

    /**
     * @param int $replyId
     * @return ReplyInterface
     */
    public function setReplyId(int $replyId) : ReplyInterface;

    /**
     * @param int $feedbackId
     * @return ReplyInterface
     */
    public function setFeedbackId(int $feedbackId) : ReplyInterface;

    /**
     * @return mixed
     */
    public function setAdminId(int $adminId) : ReplyInterface;


    /**
     * @param string|null $replyText
     * @return ReplyInterface
     */
    public function setReplyText(?string $replyText) : ReplyInterface;

    /**
     * @param string $replyCreationTime
     * @return ReplyInterface
     */
    public function setReplyCreationTime(string $replyCreationTime) : ReplyInterface;

    /**
     * @return mixed
     */
    public function setReplyUpdateTime(string $replyUpdateTime) : ReplyInterface;
}

