<?php

namespace Training\Feedback\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\ResourceModel\Feedback;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Helper\Email;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 *
 */
class Save extends Action
{
    /**
     *
     */
    private const FEEDBACK_EDIT_PAGE_PATH = 'admin/training_feedback/index/edit/feedback_id/';
    /**
     *
     */
    private const PUBLISH_FEEDBACK_PATH = 'feedback_configuration/feedback_configuration_general/publish_feedback_without_moderation';

    /**
     * @var FeedbackFactory
     */
    private $feedbackFactory;
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
    private $urlInterface;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var FeedbackRepositoryInterface
     */
    private $feedbackRepository;

    /**
     * @param Context $context
     * @param FeedbackFactory $feedbackFactory
     * @param Feedback $feedbackResource
     * @param Email $email
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        Context $context,
        FeedbackFactory $feedbackFactory,
        Feedback $feedbackResource,
        Email $email,
        UrlInterface $urlInterface,
        ScopeConfigInterface $scopeConfig,
        FeedbackRepositoryInterface $feedbackRepository
    ) {
        $this->feedbackFactory = $feedbackFactory;
        $this->feedbackResource = $feedbackResource;
        $this->email = $email;
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->feedbackRepository = $feedbackRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultRedirectFactory->create();

        if ($post = $this->getRequest()->getPostValue()) {
            try {
                // input data validation
                $this->validatePost($post);

                // create model instance
                $feedback = $this->feedbackFactory->create();

                $this->setDataToModel($feedback, $post);

                // save data
                $this->feedbackRepository->save($feedback);

                $this->email->sendEmail(
                    $post['message'],
                    $this->getLinkToFeedbackEditPage($feedback));

                $this->messageManager->addSuccessMessage(
                    __('Thank you for your feedback.')
                );

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('An error occurred while processing your form. Please try again later.')
                );

                $result->setPath('*/*/form');
                return $result;
            }
        }
        $result->setPath('*/*/index');
        return $result;
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
        if (trim($this->getRequest()->getParam('hideit')) !== '') {
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
