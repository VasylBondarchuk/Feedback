<?php
declare(strict_types=1);

namespace Training\Feedback\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class FeedbackConfigProvider extends AbstractHelper
{
    private const XML_PATH_FEEDBACK_CONFIGURATION_SECTION = 'feedback_configuration/';    
    private const XML_PATH_GENERAL_CONFIGURATION_GROUP = 'feedback_configuration_general/';
    private const XML_PATH_RATINGS_CONFIGURATION_GROUP = 'feedback_ratings_configuration/';
    private const XML_PATH_EMAIL_NOTIFICATIONS_CONFIGURATION_GROUP = 'feedback_configuration_email_notifications/';
    private const XML_PATH_FEEDBACK_REPLY_APPEARANCE_CONFIGURATION_GROUP = 'feedback_configuration_appearance/';
    
    
    /**
     * Get configuration value by field
     * 
     * @param string $field
     * @param string $scope
     * @return string|null
     */
    public function getConfigValue(string $group, string $field, $scope = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_FEEDBACK_CONFIGURATION_SECTION . $group . $field, $scope);
    }
    
    /**
     * Get top menu link name
     * 
     * @return string|null
     */
    public function getTopMenuLinkName() : ?string
    {
        return $this->getConfigValue(self::XML_PATH_GENERAL_CONFIGURATION_GROUP , 'top_menu_link_name');
    }

    /**
     * Get maximum rating value
     * 
     * @return string|null
     */
    public function getRatingMaxValue() : ?string
    {
        return $this->getConfigValue(self::XML_PATH_RATINGS_CONFIGURATION_GROUP , 'rating_max_value');
    }

    /**
     * Check if ratings are enabled
     * 
     * @return bool
     */
    public function isRatingsEnabled() : bool
    {
        return $this->getConfigValue(self::XML_PATH_RATINGS_CONFIGURATION_GROUP , 'enable_feedback_ratings') == '1';
    }

    /**
     * Get feedback background color
     * 
     * @return string|null
     */
    public function getBackgroundColor() : ?string
    {
        return $this->getConfigValue(self::XML_PATH_FEEDBACK_REPLY_APPEARANCE_CONFIGURATION_GROUP , 'feedback_background_color');
    }  
    
}
