<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;

class MassStatus extends Action implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';

    protected $messageManager;
    protected $resultFactory;
    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private FeedbackRepositoryInterface $feedbackRepository;
    private RequestInterface $request;
    private ?LoggerInterface $logger;

    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        Filter $filter,
        CollectionFactory $collectionFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        RequestInterface $request,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context);
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function execute(): ResultInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        [$feedbackStatus, $feedbackStatusError] = $this->updateFeedbackStatus($collection);

        $this->addMessages($feedbackStatus, $feedbackStatusError);

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }

    private function updateFeedbackStatus($collection): array
    {
        $feedbackStatus = 0;
        $feedbackStatusError = 0;
        $isActive = (int) $this->request->getParam('is_active');

        foreach ($collection->getItems() as $feedback) {
            try {
                $this->feedbackRepository->save($feedback->setIsActive($isActive));
                $feedbackStatus++;
            } catch (LocalizedException $exception) {
                $this->logger?->error($exception->getMessage());
                $feedbackStatusError++;
            } catch (\Exception $exception) {
                $this->logger?->error($exception->getMessage());
                $feedbackStatusError++;
            }
        }

        return [$feedbackStatus, $feedbackStatusError];
    }

    private function addMessages(int $feedbackStatus, int $feedbackStatusError): void
    {        
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
    }
}
