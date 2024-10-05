<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;


class MassDelete implements ActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_delete';

    /**
     * 
     * @var type
     */
    private ManagerInterface $messageManager;
    /**
     * 
     * @var type
     */
    private ResultFactory $resultFactory;
    /**
     * 
     * @var Filter
     */
    private Filter $filter;
    /**
     * 
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;
    /**
     * 
     * @var FeedbackRepositoryInterface
     */
    private FeedbackRepositoryInterface $feedbackRepository;
    /**
     * 
     * @var ReplyRepositoryInterface
     */
    private ReplyRepositoryInterface $replyRepository;
    /**
     * 
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;   
    /**
     * 
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;

    public function __construct(        
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        ReplyRepositoryInterface $replyRepository,
        LoggerInterface $logger = null,
        AuthorizationInterface $authorization    
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->replyRepository = $replyRepository;
        $this->logger = $logger; 
        $this->authorization = $authorization;
    }

    /**
     * 
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        
        // Check if the admin user has the required permission
        if (!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            $this->messageManager->addErrorMessage(__('You are not authorized to delete feedbacks.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                            ->setUrl($this->urlBuilder->getUrl('*/*/'));
        }
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        list($feedbackDeleted, $feedbackDeletedError) = $this->processFeedbackCollection($collection);
        $this->addMessages($feedbackDeleted, $feedbackDeletedError);

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }

    /**
     * 
     * @param type $collection
     * @return array
     */
    private function processFeedbackCollection($collection): array
    {
        $feedbackDeleted = 0;
        $feedbackDeletedError = 0;

        foreach ($collection as $feedback) {
            try {
                $this->feedbackRepository->delete($feedback);
                $this->replyRepository->deleteByFeedbackId($feedback->getFeedbackId());
                $feedbackDeleted++;
            } catch (LocalizedException $exception) {
                $this->logger?->error($exception->getMessage());
                $feedbackDeletedError++;
            } catch (\Exception $exception) {
                $this->logger?->error($exception->getMessage());
                $feedbackDeletedError++;
            }
        }

        return [$feedbackDeleted, $feedbackDeletedError];
    }

    /**
     * 
     * @param int $feedbackDeleted
     * @param int $feedbackDeletedError
     * @return void
     */    
    private function addMessages(int $feedbackDeleted, int $feedbackDeletedError): void
    {
        if ($feedbackDeleted) {
            $this->messageManager->addSuccessMessage(
                __('%1 record(s) have been deleted.', $feedbackDeleted)
            );
        }
        if ($feedbackDeletedError) {
            $this->messageManager->addErrorMessage(
                __(
                    '%1 record(s) haven\'t been deleted. Please see server logs for more details.',
                    $feedbackDeletedError
                )
            );
        }
    }
}
