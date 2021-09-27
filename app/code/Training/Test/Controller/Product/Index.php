<?php

namespace Training\Test\Controller\Product;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * This class will redirect not logged user from the product page to login page
 */
class Index implements HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @param PageFactory $pageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     */
    public function __construct(
        PageFactory $pageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->customerSession = $customerSession;
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->redirectFactory->create()->setPath('customer/account/login');
        }

        $page = $this->pageFactory->create();
        return $page;
    }
}
