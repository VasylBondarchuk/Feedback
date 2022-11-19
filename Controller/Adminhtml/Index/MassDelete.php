<?php

declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 *
 */
class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * Mass-actions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;


    /**
     * @var FeedbackRepositoryInterface
     */
    private $feedbackRepository;

    /**
     * @var ReplyRepositoryInterface
     */
    private $replyRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context                   $context,
        Filter                    $filter,
        CollectionFactory         $collectionFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        ReplyRepositoryInterface $replyRepository,
        LoggerInterface           $logger = null
    ) {
        
        parent::__construct($context);
        
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->replyRepository = $replyRepository;
        $this->logger = $logger;
    }

    /**
     * Mass Delete Action
     *
     * @return Redirect
     * @throws LocalizedException
     */

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $feedbackDeleted = 0;
        $feedbackDeletedError = 0;

        foreach ($collection as $feedback) {
            try {
                $this->feedbackRepository->delete($feedback);
                $this->replyRepository->deleteByFeedbackId($feedback->getFeedbackId());
                $feedbackDeleted++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $feedbackDeletedError++;
            }
        }

        if ($feedbackDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $feedbackDeleted)
            );
        }

        if ($feedbackDeletedError) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been deleted. Please see server logs for more details.',
                    $feedbackDeletedError
                )
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('training_feedback/*/index');
    }
}
