<?php

namespace Training\Feedback\Helper\EmailNotifications;

/**
 * Sends email notification in the case new feedback is submitted
 */
class ReplyEmailNotification extends EmailNotification
{
    protected const TEMPLATE_ID = 'frontend_reply_notification';
    protected const TEMPLATE_VARS_NAMES = ['recipientName','replyText'];
}
