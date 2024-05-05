<?php

declare(strict_types=1);

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
use Magento\Store\Model\StoreManagerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Helper\EmailNotifications\FeedbackEmailNotification;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\FeedbackFactory;
use Magento\Backend\Model\UrlInterface;
use Psr\Log\LoggerInterface;
use Training\Feedback\Helper\Form;

/**
 * Saves new feedback
 */
class Save implements HttpPostActionInterface {

    private const FEEDBACK_EDIT_PAGE_PATH = 'training_feedback/index/edit/feedback_id/';
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

    /**
     * @var FeedbackEmailNotification
     */
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

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    /**
     * 
     * @var Form
     */
    private Form $form;


    /**
     * 
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param FeedbackFactory $feedbackFactory
     * @param FeedbackEmailNotification $email
     * @param UrlInterface $urlInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param Form $form
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
            StoreManagerInterface $storeManager,
            LoggerInterface $logger,
            Form $form
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
        $this->logger = $logger;
        $this->form = $form;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute() {        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        if ($this->form->isFormSubmitted()) {
            $post = $this->form->getFormData();
            try {
                // input data validation
                $this->form->validatePost($post);
                $this->saveFeedback($post);
                // sends email notification about submitting new feedback                
                $this->sendNewFeedbackNotificationEmail($post['message']);
                $this->messageManager->addSuccessMessage(
                        __('Thank you for your feedback.')
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                        __('An error occurred while processing your form. %1', $e->getMessage())
                );
                // Log a message
                $this->logger->error($e->getMessage());
                $resultRedirect->setPath('*/*/form');
            }
        }
        return $resultRedirect;
    }
    
    /**
     * 
     * @param array $post
     * @return void
     */    
    private function saveFeedback(array $post): void {
        // create feedback model instance
        $feedback = $this->feedbackFactory->create();                
        // set data to model
        $this->populateFeedbackModel($feedback, $post);
        // save data
        $this->feedbackRepository->save($feedback);     
    }   
    

    /**
     * @param FeedbackModel $feedback
     * @param array $post
     * @return void
     * @throws NoSuchEntityException
     */
    private function populateFeedbackModel(FeedbackModel $feedback, array $post): void {
        $feedback
                ->setData($post)
                ->setIsActive($this->publishFeedbackWithoutModeration())
                ->setStoreId((int) $this->storeManager->getStore()->getId());
        if (!isset($post['reply_notification'])) {
            $feedback->setReplyNotification(0);
        }
        if ($this->customerSession->isLoggedIn()) {
            $feedback->setCustomerId((int) $this->customerSession->getCustomerId());
        }
    }

    /**
     * @return string|null
     */
    private function publishFeedbackWithoutModeration(): ?string {
        return $this->scopeConfig->getValue(self::PUBLISH_FEEDBACK_PATH);
    }    
    
    private function sendNewFeedbackNotificationEmail(string $message){        
        $feedback = $this->feedbackFactory->create();
        if($this->email->getNotificationRecipientEmail() && $this->email->getNotificationRecipientName()){
            $this->email->sendEmail(
                        $this->email->getNotificationRecipientEmail(),
                        [$this->email->getNotificationRecipientName(),
                        $message, 
                        $this->getLinkToFeedbackEditPage($feedback)]
                );
        }
    }
    
    /**
     * @param FeedbackModel $feedback
     * @return string
     */
    private function getLinkToFeedbackEditPage(FeedbackModel $feedback): string {
        return $this->urlInterface->getRouteUrl(self::FEEDBACK_EDIT_PAGE_PATH,
                        [
                            'feedback_id' => $feedback->getFeedbackId(),
                            'key' => $this->urlInterface->getSecretKey('training_feedback', 'index', 'edit')
                        ]
                );
    } 

}
