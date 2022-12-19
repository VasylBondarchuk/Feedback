<?php

namespace Training\Feedback\Helper\EmailNotifications;

use Magento\Store\Model\ScopeInterface;

/**
 * Sends email notification in the case new feedback is submitted
 */
class FeedbackEmailNotification extends EmailNotification
{
    private const NEW_FEEDBACK_NOTIFICATION_EMAIL_PATH =
        'feedback_configuration/feedback_configuration_general/admin_email_new_feedback_notification';
    private const NEW_FEEDBACK_NOTIFICATION_NAME_PATH =
        'feedback_configuration/feedback_configuration_general/admin_name_new_feedback_notification';
    protected const TEMPLATE_ID = 'frontend_new_feedback_notification';

    protected const TEMPLATE_VARS_NAMES= ['recipientName','feedbackText','link'];

    /**
     * @param string $path
     * @return string
     */
    public function getConfigsValue(string $path): string
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getNotificationRecipientEmail(): string
    {
        return $this->getConfigsValue(self::NEW_FEEDBACK_NOTIFICATION_EMAIL_PATH);
    }

    /**
     * @return string
     */
    public function getNotificationRecipientName(): string
    {
        return $this->getConfigsValue(self::NEW_FEEDBACK_NOTIFICATION_NAME_PATH);
    }
}
