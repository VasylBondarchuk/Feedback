<?php

namespace Training\Feedback\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 *
 */
class Customer extends AbstractHelper
{
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @param Context $context
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ManagerInterface $messageManager
    )
    {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->urlInterface = $context->getUrlBuilder();
        $this->messageManager = $messageManager;
    }

    /**
     * @return void
     */
    public function redirectIfNotLoggedIn(string $message = '') : void
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->urlInterface->getCurrentUrl());
            $this->customerSession->authenticate();
            $this->messageManager->addErrorMessage(__($message));
        }
    }
}
