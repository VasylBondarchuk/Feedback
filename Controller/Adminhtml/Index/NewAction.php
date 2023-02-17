<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Creates new feedback
 */
class NewAction extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::menu';

    protected $resultFactory;

    private DataPersistorInterface $dataPersistor;

    /**
     * @param ResultFactory $resultFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        DataPersistorInterface $dataPersistor
    ) {
        $this->resultFactory = $resultFactory;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
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
