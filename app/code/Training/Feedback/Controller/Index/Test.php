<?php

namespace Training\Feedback\Controller\Index;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Training\Feedback\Api\Data\FeedbackInterfaceFactory;
use Training\Feedback\Api\Data\FeedbackRepositoryInterface;

class Test extends Action
{
    private $feedbackFactory;
    private $feedbackRepository;
    private $searchCriteriaBuilder;
    private $sortOrderBuilder;
    public function __construct(
        Context $context,
        FeedbackInterfaceFactory $feedbackFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->feedbackFactory = $feedbackFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        parent::__construct($context);
    }
    public function execute()
    {
// create new item
        $newFeedback = $this->feedbackFactory->create();
        $newFeedback->setAuthorName('some name');
        $newFeedback->setAuthorEmail('test@test.com');
        $newFeedback->setMessage('Test message 1');
        $newFeedback->setIsActive(1);
        $this->feedbackRepository->save($newFeedback);
        // load item by id
        $feedback = $this->feedbackRepository->getById(28);
        $this->printFeedback($feedback);
        // update item
        $feedbackToUpdate = $this->feedbackRepository->getById(28);
        $feedbackToUpdate->setMessage('CUSTOM ' . $feedbackToUpdate->getMessage());
        // delete feedback
        $this->feedbackRepository->deleteById(28);
        // load multiple items
        $this->searchCriteriaBuilder
            ->addFilter('is_active', 1);
        $sortOrder = $this->sortOrderBuilder
            ->setField('message')
            ->setAscendingDirection()
            ->create();
        $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResult = $this->feedbackRepository->getList($searchCriteria);
        foreach ($searchResult->getItems() as $item) {
            $this->printFeedback($item);
        }
        exit();
    }
    private function printFeedback($feedback)
    {
        echo $feedback->getId() . ' : '
            . $feedback->getAuthorName()
            . ' (' . $feedback->getAuthorEmail() . ')';
        echo "<br/>\n";
    }
}
