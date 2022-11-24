<?php

namespace Training\Feedback\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\ResourceModel\Feedback;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Helper\Email;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

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
    private $messageManager;
    /**
     * @var ResultFactory
     */
    private $resultFactory;
    /**
     * @var FeedbackFactory
     */
    private FeedbackFactory $feedbackFactory;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Feedback
     */
    private $feedbackResource;
    /**
     * @var Email
     */
    private $email;
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
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param FeedbackFactory $feedbackFactory
     * @param Feedback $feedbackResource
     * @param Email $email
     * @param UrlInterface $urlInterface
     * @param ScopeConfigInterface $scopeConfig
     * @param FeedbackRepositoryInterface $feedbackRepository
     */
    public function __construct(
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        RequestInterface $request,
        FeedbackFactory $feedbackFactory,
        Feedback $feedbackResource,
        Email $email,
        UrlInterface $urlInterface,
        ScopeConfigInterface $scopeConfig,
        FeedbackRepositoryInterface $feedbackRepository
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->feedbackFactory = $feedbackFactory;
        $this->feedbackResource = $feedbackResource;
        $this->email = $email;
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\Result\Redirect|ResultInterface
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
                    $post['message'], $this->getLinkToFeedbackEditPage($feedback)
                );
                $this->messageManager->addSuccessMessage(
                    __('Thank you for your feedback.'));
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
     */
    private function setDataToModel(FeedbackModel $feedback, array $post)
    {
        $feedback
            ->setData($post)
            ->setIsActive($this->publishFeedbackWithoutModeration());
    }

    /**
     * @return string
     */
    private function publishFeedbackWithoutModeration(): string
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
