<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Ratingoption;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Training\Feedback\Model\ResourceModel\Feedback as Resource;

/**
 * Index page
 */
class Index extends Action implements HttpGetActionInterface
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_view';

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

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
        Context $context,
        ResultFactory $resultFactory,
        DataPersistorInterface    $dataPersistor,        
        Resource $resource,        
        ManagerInterface $messageManager
    ) {
        $this->resultFactory = $resultFactory;
        $this->dataPersistor = $dataPersistor;
        $this->resource = $resource;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {       

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->getConfig()->getTitle()->prepend(__('Rating Options'));        

        return $resultPage;
    }   
}