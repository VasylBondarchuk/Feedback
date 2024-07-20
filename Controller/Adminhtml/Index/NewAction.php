<?php

declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Creates new feedback
 */
class NewAction extends Action implements HttpGetActionInterface {

    const ADMIN_RESOURCE = 'Training_Feedback::menu';

    protected $resultFactory;

    /**
     * 
     * @param Context $context
     * @param ResultFactory $resultFactory
     */
    public function __construct(
            Context $context,
            ResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     */
    public function execute() {
        $feedbackId = $this->getRequest()->getParam('feedback_id');
        //$this->_coreRegistry->register('current_feedback_id', $feedbackId);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($feedbackId === null) {
            $resultPage->addBreadcrumb(__('New Feedback'),
                    __('New Feedback'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Feedback'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Feedback'),__('Edit Feedback'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Feedback'));
        }
        // Build the edit form
        /*$resultPage->getLayout()->addBlock(
                        'Training\Feedback\Block\Adminhtml\Feedback\Edit','feedback', 'content');*/
        return $resultPage;
    }
}
