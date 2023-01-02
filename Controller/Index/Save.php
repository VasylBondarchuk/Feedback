<?php

namespace Training\Feedback\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Helper\EmailNotifications\FeedbackEmailNotification;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\FeedbackFactory;

/**
 * Saves new feedback
 */
class Save implements HttpPostActionInterface
{
    private const FEEDBACK_EDIT_PAGE_PATH = 'admin/training_feedback/index/edit/feedback_id/';

    private const PUBLISH_FEEDBACK_PATH = 'feedback_configuration/feedback_configuration_general/publish_feedback_without_moderation';

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;
    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;
    /**
     * @var FeedbackFactory
     */
    private FeedbackFactory $feedbackFactory;
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    private FeedbackEmailNotification $email;
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlInterface;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var FeedbackRepositoryInterface
     */
    private FeedbackRepositoryInterface $feedbackRepository;

    private Session $customerSession;

    private StoreManagerInterface $storeManager;

    /**
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param FeedbackFactory $feedbackFactory
     * @param FeedbackEmailNotification $email
     * @param UrlInterface $urlInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param Session $customerSession
     */
    public function __construct(
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        RequestInterface $request,
        FeedbackFactory $feedbackFactory,
        FeedbackEmailNotification $email,
        UrlInterface $urlInterface,
        ScopeConfigInterface $scopeConfig,
        FeedbackRepositoryInterface $feedbackRepository,
        Session $customerSession,
        StoreManagerInterface $storeManager
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->feedbackFactory = $feedbackFactory;
        $this->email = $email;
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->feedbackRepository = $feedbackRepository;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        if ($post = $this->request->getPostValue()) {
            try {
                // input data validation
                $this->validatePost($post);
                // create feedback model instance
                $feedback = $this->feedbackFactory->create();
                // set data to model
                $this->setDataToModel($feedback, $post);
                // save data
                $this->feedbackRepository->save($feedback);
                // sends email notification about submitting new feedback
                $this->email->sendEmail(
                    $this->email->getNotificationRecipientEmail(),
                    [$this->email->getNotificationRecipientName(),
                    $post['message'], $this->getLinkToFeedbackEditPage($feedback)]
                );
                $this->messageManager->addSuccessMessage(
                    __('Thank you for your feedback.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('An error occurred while processing your form. Please try again later.')
                );
                $resultRedirect->setPath('*/*/form');
            }
        }
        return $resultRedirect;
    }
    /**
     * @param $post
     * @return void
     * @throws LocalizedException
     */
    private function validatePost($post)
    {
        if (!isset($post['author_name']) || trim($post['author_name']) === '') {
            throw new LocalizedException(__('Name is missing'));
        }
        if (!isset($post['message']) || trim($post['message']) === '') {
            throw new LocalizedException(__('Comment is missing'));
        }
        if (!isset($post['author_email']) || false === \strpos($post['author_email'], '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }
        if (trim($this->request->getParam('hideit')) !== '') {
            throw new \Exception();
        }
    }

    /**
     * @param FeedbackModel $feedback
     * @return int
     */
    private function getFeedbackId(FeedbackModel $feedback) : int
    {
        return ($feedback->getFeedbackId());
    }

    /**
     * @param FeedbackModel $feedback
     * @param array $post
     * @return void
     * @throws NoSuchEntityException
     */
    private function setDataToModel(FeedbackModel $feedback, array $post): void
    {
        $feedback
            ->setData($post)
            ->setIsActive($this->publishFeedbackWithoutModeration())
            ->setStoreId($this->storeManager->getStore()->getId());
        if (!isset($post['reply_notification'])) {
            $feedback->setReplyNotification(0);
        }
        if ($this->customerSession->isLoggedIn()) {
            $feedback->setCustomerId($this->customerSession->getCustomerId());
        }
    }

    /**
     * @return string|null
     */
    private function publishFeedbackWithoutModeration(): ?string
    {
        return $this->scopeConfig->getValue(self::PUBLISH_FEEDBACK_PATH);
    }

    /**
     * @param FeedbackModel $feedback
     * @return string
     */
    private function getLinkToFeedbackEditPage(FeedbackModel $feedback) : string
    {
        return $this->urlInterface->getUrl(self::FEEDBACK_EDIT_PAGE_PATH) . $this->getFeedbackId($feedback);
    }
}
