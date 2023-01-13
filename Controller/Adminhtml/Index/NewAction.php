<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 *
 */
class NewAction implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::menu';

    private ResultFactory $resultFactory;

    private DataPersistorInterface $dataPersistor;

    /**
     * @param ResultFactory $resultFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        ResultFactory $resultFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->resultFactory = $resultFactory;
        $this->dataPersistor = $dataPersistor;
    }
    /**
     * Index action
     *
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->getConfig()->getTitle()->prepend(__('Create New Feedback'));
        $this->dataPersistor->clear('training_feedback');
        return $resultPage;
    }
}
