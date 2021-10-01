<?php

namespace Training\Test\Controller\Product;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Helper\Product\View as ProductHelper;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Json\Helper\Data;

/**
 * This class will redirect not logged user from the product page to login page
 */

class View extends \Magento\Catalog\Controller\Product\View
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Session $customerSession
     * @param Context $context
     * @param ProductHelper $productHelper
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param LoggerInterface|null $logger
     * @param Data|null $jsonHelper
     * @param EventManager $eventManager
     */
    public function __construct(
        Session $customerSession,
        Context $context,
        ProductHelper $productHelper,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        LoggerInterface $logger = null,
        Data $jsonHelper = null,
        EventManager $eventManager
    )
    {
        $this->customerSession = $customerSession;
        parent::__construct(
            $context,
            $productHelper,
            $resultForwardFactory,
            $resultPageFactory,
            $logger,
            $jsonHelper,
            $eventManager
        );
    }

    /**
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/account/login');

        if(!$this->customerSession->isLoggedIn()) {
            return $resultRedirect;
        }
        return parent::execute();
    }
}
