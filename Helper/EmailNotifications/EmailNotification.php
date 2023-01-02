<?php

namespace Training\Feedback\Helper\EmailNotifications;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Sends email notification in the case new feedback is submitted
 */
class EmailNotification extends AbstractHelper
{
    protected const TEMPLATE_ID = '';
    protected const SENDER_DETAILS_NAMES = ['name','email'];
    protected const TEMPLATE_VARS_NAMES = [];
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
    protected function getConfigsValue(string $path): ?string
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
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
        return $this->getEmailDetails(self::SENDER_DETAILS_NAMES, $senderDetailsValues);
    }

    /**
     * @param array $templateVarValues
     * @return array
     */
    public function getTemplateVars(array $templateVarValues) : array
    {
        return $this->getEmailDetails(static::TEMPLATE_VARS_NAMES, $templateVarValues);
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
     * @param string $recipientEmail
     * @param array $templateVarValues
     * @return void
     */
    public function sendEmail( string $recipientEmail, array $templateVarValues): void
    {
        try {
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(static::TEMPLATE_ID)
                ->setTemplateOptions($this->getTemplateOptions())
                ->setTemplateVars($this->getTemplateVars($templateVarValues))
                ->setFromByScope($this->getSenderDetails(['Online Store','online@gmail.com']))
                ->addTo($recipientEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
