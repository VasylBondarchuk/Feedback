<?php

declare(strict_types = 1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class MassStatus extends Action implements HttpPostActionInterface
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;


    private $feedbackRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(
        Context                   $context,
        Filter                    $filter,
        CollectionFactory         $collectionFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        LoggerInterface           $logger = null
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->feedbackRepository = $feedbackRepository;
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
        $feedbackStatus = 0;
        $feedbackStatusError = 0;
        $isActive = (int)($this->getRequest()->getParam('is_active'));

        foreach ($collection->getItems() as $feedback) {
            try {
                $this->feedbackRepository->save($feedback->setIsActive($isActive));
                $feedbackStatus++;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception->getLogMessage());
                $feedbackStatusError++;
            }
        }

        if ($feedbackStatus) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been updated.', $feedbackStatus)
            );
        }

        if ($feedbackStatusError) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) haven\'t been updated. Please see server logs for more details.',
                    $feedbackStatusError
                )
            );
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('training_feedback/*/index');
    }
}
