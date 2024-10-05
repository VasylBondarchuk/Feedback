<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\AuthorizationInterface;

class MassStatus implements ActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';

    /**
     * 
     * @var type
     */
    private $messageManager;
    /**
     * 
     * @var type
     */
    private $resultFactory;
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
     * @var RequestInterface
     */
    private RequestInterface $request;
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
        RequestInterface $request,
        LoggerInterface $logger = null,
        AuthorizationInterface $authorization
            
    ) {        
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->request = $request;
        $this->logger = $logger;
        $this->authorization = $authorization;
    }

    public function execute(): ResultInterface
    {
        // Check if the admin user has the required permission
        if (!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            $this->messageManager->addErrorMessage(__('You are not authorized to change feedbacks\' status.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                            ->setUrl($this->urlBuilder->getUrl('*/*/'));
        }
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
