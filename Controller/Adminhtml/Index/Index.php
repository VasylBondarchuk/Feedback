<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\AuthorizationInterface;
use Training\Feedback\Model\ResourceModel\Feedback as Resource;

/**
 * Index page
 */
class Index implements ActionInterface
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
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Resource
     */
    private Resource $resource;
    
     /**
     * 
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;

    /**
     * @param ResultFactory $resultFactory
     * @param DataPersistorInterface $dataPersistor    
     * @param Resource $resource     
     * @param ManagerInterface $messageManager
     */
    public function __construct(        
        ResultFactory $resultFactory,               
        Resource $resource,        
        ManagerInterface $messageManager,
        AuthorizationInterface $authorization    
    ) {
        $this->resultFactory = $resultFactory;       
        $this->resource = $resource;
        $this->messageManager = $messageManager;
        $this->authorization = $authorization;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        
        // Check if the admin user has the required permission
        if (!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            $this->messageManager->addErrorMessage(__('You are not authorized to view feedbacks.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                            ->setUrl($this->urlBuilder->getUrl('*/*/'));
        }
        
        $this->displayNotPublishedFeedbacksNumber();
        $this->displayNotRepliedFeedbacksNumber();

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Feedbacks')); 
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
                __('%1 Feedback(s) are published but not replied.',
                    $this->resource->getNotRepliedFeedbacksNumber())
            );
        }
    }
}