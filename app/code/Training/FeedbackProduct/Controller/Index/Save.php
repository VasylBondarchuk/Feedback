<?php

namespace Training\FeedbackProduct\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Model\ResourceModel\Feedback;
use Training\FeedbackProduct\Model\FeedbackDataLoader;
use Training\Feedback\Controller\Index\Save as OverrideSave;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface;


class Save extends OverrideSave
{
    private $feedbackFactory;
    private $feedbackResource;
    private $feedbackDataLoader;
    private $logger;

    public function __construct(
        Context $context,
        FeedbackFactory $feedbackFactory,
        Feedback $feedbackResource,
        FeedbackDataLoader $feedbackDataLoader,
        EventManager $eventManager,
        LoggerInterface $logger

    ) {
        $this->feedbackFactory = $feedbackFactory;
        $this->feedbackDataLoader = $feedbackDataLoader;
        $this->feedbackResource = $feedbackResource;
        $this->eventManager = $eventManager;
        $this->logger = $logger;

        parent::__construct($context,$feedbackFactory,$feedbackResource);
    }
    public function execute()
    {
        $result = $this->resultRedirectFactory->create();

        if ($post = $this->getRequest()->getPostValue()) {
            try {
                $this->validatePost($post);
                $feedback = $this->feedbackFactory->create();
                $feedback->setData($post);
                $this->setProductsToFeedback($feedback, $post);
                $this->feedbackResource->save($feedback);
                $this->eventManager->dispatch('training_feedback_save_after', ['feedback' => $feedback]);
                $this->messageManager->addSuccessMessage(
                    __('Thank you for your feedback.')
                );
            } catch (\Exception $e) {
                $this->logger->critical('Error message', ['exception' => $e]);
                $this->messageManager->addErrorMessage(
                    __('An error occurred while processing your form. Please try again later.')
                );
                $result->setPath('*/*/form');
            }
        }

        $result->setPath('*/*/index');
        return $result;
    }
    private function setProductsToFeedback($feedback, $post)
    {
        $skus = [];
        if (isset($post['products_skus']) && !empty($post['products_skus'])) {
            $skus = explode(',', $post['products_skus']);
            $skus = array_map('trim', $skus);
            $skus = array_filter($skus);
        }

        $this->feedbackDataLoader->addProductsToFeedbackBySkus($feedback, $skus);
    }

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
}
