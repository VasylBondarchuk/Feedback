<?php
declare(strict_types = 1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;

/**
 *
 */
class MassStatus extends Action implements HttpPostActionInterface 
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var FeedbackRepositoryInterface
     */
    private FeedbackRepositoryInterface $feedbackRepository;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    /**
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param RequestInterface $request
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        Filter                    $filter,
        CollectionFactory         $collectionFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        RequestInterface $request,
        LoggerInterface           $logger = null
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->request = $request;
        $this->logger = $logger;
        parent::__construct($context);
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
        $isActive = (int)($this->request->get('is_active'));

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
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
