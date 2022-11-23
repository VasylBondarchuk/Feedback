<?php

namespace Training\Feedback\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Training\Feedback\Helper\Customer;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 *
 */
class Form implements HttpGetActionInterface
{
    private const ADD_FEEDBACK_BY_GUESTS =
        'feedback_configuration/feedback_configuration_general/add_feedback_by_guests';
    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var Customer
     */
    private $customerRedirect;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param PageFactory $pageFactory
     * @param Customer $customerRedirect
     * @param ScopeConfigInterface $scopeConfig
     */

    public function __construct(
        PageFactory $pageFactory,
        Customer $customerRedirect,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->pageFactory = $pageFactory;
        $this->customerRedirect = $customerRedirect;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return Page|ResultInterface
     */
    public function execute()
    {
        if(!$this->isGuestAllowedToAddFeedback()){
            $this->customerRedirect->redirectIfNotLoggedIn('You must login or register to add your feedback');;
        }
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__('Feedback form'));
        return $page;
    }

    /**
     * @return bool
     */
    public function isGuestAllowedToAddFeedback(): bool
    {
        return $this->scopeConfig->getValue(self::ADD_FEEDBACK_BY_GUESTS, ScopeInterface::SCOPE_STORE);
    }
}
