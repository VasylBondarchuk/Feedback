<?php

namespace Training\Feedback\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Sends email notification in the case new feedback is submitted
 */
class FeedbackEmailNotification extends AbstractHelper
{
    private const NEW_FEEDBACK_NOTIFICATION_EMAIL_PATH =
        'feedback_configuration/feedback_configuration_general/admin_email_new_feedback_notification';
    private const NEW_FEEDBACK_NOTIFICATION_NAME_PATH =
        'feedback_configuration/feedback_configuration_general/admin_name_new_feedback_notification';
    private const TEMPLATE_ID = 'frontend_new_feedback_notification';

    /**
     * @var StateInterface
     */
    protected StateInterface $inlineTranslation;
    /**
     * @var Escaper
     */
    protected Escaper $escaper;
    /**
     * @var TransportBuilder
     */
    protected TransportBuilder $transportBuilder;
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param Context $context
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

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

    /**
     * @param array $emailDetailsNames
     * @param array $emailDetailsValues
     * @return array
     */
    public function getEmailDetails(array $emailDetailsNames, array $emailDetailsValues) : array
    {
        return array_combine($emailDetailsNames, $emailDetailsValues);
    }

    /**
     * @param array $senderDetailsValues
     * @return array
     */
    public function getSenderDetails(array $senderDetailsValues) : array
    {
        return $this->getEmailDetails(['name','email'], $senderDetailsValues);
    }

    /**
     * @param array $templateVarValues
     * @return array
     */
    public function getTemplateVars(array $templateVarValues) : array
    {
        return $this->getEmailDetails(['recipientName','feedbackText','link'], $templateVarValues);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getTemplateOptions() : array
    {
        return $this->getEmailDetails(
            ['area', 'store'],
            [Area::AREA_ADMINHTML, $this->storeManager->getStore()->getId()]
        );
    }

    /**
     * @return void
     */
    public function sendEmail(string $feedbackMessage, string $link)
    {
        try {
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(self::TEMPLATE_ID)
                ->setTemplateOptions($this->getTemplateOptions())
                ->setTemplateVars($this->getTemplateVars([$this->getNotificationRecipientName(), $feedbackMessage, $link]))
                ->setFromByScope($this->getSenderDetails(['Online Store','online@gmail.com']))
                ->addTo($this->getNotificationRecipientEmail())
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();

        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
