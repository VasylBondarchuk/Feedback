<?php

namespace Training\Feedback\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback';

    private $resultPageFactory;
    private $dataPersistor;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }
    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->addBreadcrumb(__('Feedbacks'), __('Feedbacks'))
            ->getConfig()->getTitle()->prepend(__('Feedback'));
        $this->dataPersistor->clear('training_feedback');
        return $resultPage;
    }
}
