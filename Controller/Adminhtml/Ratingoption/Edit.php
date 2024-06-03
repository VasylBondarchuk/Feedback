<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Ratingoption;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\RatingOption\RatingOptionInterface;

/**
 * Edits feedback in the admin panel
 */
class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';
    const REQUEST_FIELD_NAME = 'rating_option_id';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var FeedbackRepositoryInterface
     */
    private RatingOptionInterface $ratingOptionRepository;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     *
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,    
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        RatingOptionInterface $ratingOptionRepository,
        RequestInterface $request,
        LoggerInterface  $logger           
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->ratingOptionRepository = $ratingOptionRepository;
        $this->request = $request;
        $this->logger = $logger;
        parent::__construct($context); 
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $raingOptionId = (int)($this->request->get(self::REQUEST_FIELD_NAME));
        if (!$this->isRatingOptionExist($raingOptionId)) {
            $this->messageManager->addErrorMessage(__('This rating option does not exist.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->getConfig()->getTitle()->prepend(__('Edit Rating Option'));
        return $resultPage;
    }

    /**
     * 
     * @param int $raingOptionId
     * @return bool
     */
    private function isRatingOptionExist(int $raingOptionId): bool
    {
        $exist = false;
        try {
            $this->ratingOptionRepository->getById($raingOptionId);
            $exist = true;
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage());
        }
        return $exist;
    }
}
