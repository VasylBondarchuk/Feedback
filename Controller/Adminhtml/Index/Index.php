<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Training\Feedback\Model\ResourceModel\Feedback as Resource;

/**
 * Index page
 */
class Index extends Action
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_view';

    protected $resultFactory;
    protected $messageManager;
    private Resource $resource;

    public function __construct(        
        ResultFactory $resultFactory,               
        Resource $resource,        
        ManagerInterface $messageManager,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->resource = $resource;
        $this->messageManager = $messageManager;
    }

    public function execute(): ResultInterface
    {
        if (!$this->isAllowed()) {
            $this->messageManager->addErrorMessage(__('You are not authorized to view feedbacks.'));
            return $this->_redirect('*/*/');
        }
        
        $this->displayNotPublishedFeedbacksNumber();
        $this->displayNotRepliedFeedbacksNumber();

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Feedbacks')); 
        return $resultPage;
    }

    private function displayNotPublishedFeedbacksNumber(): void
    {
        if ($this->resource->getNotPublishedFeedbacksNumber()) {
            $this->messageManager->addSuccessMessage(
                __('%1 Feedback(s) are submitted but not published yet.',
                    $this->resource->getNotPublishedFeedbacksNumber())
            );
        }
    }

    private function displayNotRepliedFeedbacksNumber(): void
    {
        if ($this->resource->getNotRepliedFeedbacksNumber()) {
            $this->messageManager->addSuccessMessage(
                __('%1 Feedback(s) are published but not replied.',
                    $this->resource->getNotRepliedFeedbacksNumber())
            );
        }
    }

    protected function isAllowed(): bool
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
