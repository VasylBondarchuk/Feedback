<?php

namespace Training\Feedback\Helper;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;

/**
 *
 */
class Customer extends AbstractHelper
{
    /**
     * @var Session
     */
    private Session $customerSession;
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlInterface;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->urlInterface = $context->getUrlBuilder();
        $this->messageManager = $messageManager;
    }

    /**
     * @param string $message
     * @return void
     * @throws SessionException
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
