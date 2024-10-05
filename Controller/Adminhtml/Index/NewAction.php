<?php

declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\AuthorizationInterface;

/**
 * Creates new feedback
 */
class NewAction implements ActionInterface {

    const ADMIN_RESOURCE = 'Training_Feedback::menu';
    /**
     * 
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;
    
    /**
     * 
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;

    /**
     * 
     * @param ResultFactory $resultFactory
     * @param AuthorizationInterface $authorization
     */
    public function __construct(            
            ResultFactory $resultFactory,
            AuthorizationInterface $authorization
    ) {
        $this->resultFactory = $resultFactory;
        $this->authorization = $authorization;        
    }

    /**
     * Index action
     *
     */
    public function execute() {               
        // Check if the admin user has the required permission
        if (!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            $this->messageManager->addErrorMessage(__('You are not authorized to add new feedback.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                            ->setUrl($this->urlBuilder->getUrl('*/*/'));
        }
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);                  
        $resultPage->getConfig()->getTitle()->prepend(__('New Feedback'));
        return $resultPage;
    }
}
