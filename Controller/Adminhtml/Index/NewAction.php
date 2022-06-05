<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 *
 */
class NewAction extends Action
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'Training_Feedback::feedback';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context                $context,
        PageFactory            $resultPageFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }
    /**
     * Index action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->addBreadcrumb(__('Create New Feedback'), __('Create New Feedback'))
            ->getConfig()->getTitle()->prepend(__('Create New Feedback'));
        $this->dataPersistor->clear('training_feedback');
        return $resultPage;
    }
}
