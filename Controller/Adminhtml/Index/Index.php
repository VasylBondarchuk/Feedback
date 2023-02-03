<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Training\Feedback\Model\ResourceModel\Feedback as Resource;

/**
 * Index page
 */
class Index implements HttpGetActionInterface
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_view';

    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var Resource
     */
    private Resource $resource;

    /**
     * @param ResultFactory $resultFactory
     * @param DataPersistorInterface $dataPersistor    
     * @param Resource $resource     
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ResultFactory $resultFactory,
        DataPersistorInterface    $dataPersistor,        
        Resource $resource,        
        ManagerInterface $messageManager
    ) {
        $this->resultFactory = $resultFactory;
        $this->dataPersistor = $dataPersistor;
        $this->resource = $resource;
        $this->messageManager = $messageManager;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        $this->displayNotPublishedFeedbacksNumber();
        $this->displayNotRepliedFeedbacksNumber();

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->getConfig()->getTitle()->prepend(__('Feedbacks'));
        $this->dataPersistor->clear('training_feedback');

        return $resultPage;
    }

    /**
     * @return void
     */
    private function displayNotPublishedFeedbacksNumber(): void
    {
        if ($this->resource->getNotPublishedFeedbacksNumber()) {
            $this->messageManager->addSuccessMessage(
                __('%1 Feedback(s) are submitted but not published yet.',
                    $this->resource->getNotPublishedFeedbacksNumber())
            );
        }
    }

    /**
     * @return void
     */
    private function displayNotRepliedFeedbacksNumber(): void
    {
        if ($this->resource->getNotRepliedFeedbacksNumber()) {
            $this->messageManager->addSuccessMessage(
                __('%1 Feedback(s) are not replied.',
                    $this->resource->getNotRepliedFeedbacksNumber())
            );
        }
    }
}
