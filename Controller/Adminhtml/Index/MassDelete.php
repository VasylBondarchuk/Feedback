<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\Framework\Controller\ResultInterface;

class MassDelete extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_delete';

    protected $messageManager;
    protected $resultFactory;
    protected Filter $filter;
    protected CollectionFactory $collectionFactory;
    protected FeedbackRepositoryInterface $feedbackRepository;
    protected ReplyRepositoryInterface $replyRepository;
    protected ?LoggerInterface $logger;

    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        ReplyRepositoryInterface $replyRepository,
        LoggerInterface $logger = null
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->replyRepository = $replyRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * 
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
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
