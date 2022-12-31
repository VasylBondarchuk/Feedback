<?php

namespace Training\Feedback\Block;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\Feedback\Model\ResourceModel\Feedback\Collection;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Magento\User\Model\UserFactory;
use Magento\User\Model\ResourceModel\User as UserResourceModel;

/**
 *
 */
class FeedbackHistory extends Template
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var UserFactory
     */
    protected UserFactory $userFactory;

    /**
     * @var UserResourceModel
     */
    protected UserResourceModel $resourceModel;

    /**
     * @var SessionFactory
     */
    private SessionFactory $customerSessionFactory;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param UserFactory $userFactory
     * @param UserResourceModel $resourceModel
     * @param SessionFactory $customerSessionFactory
     * @param array $data
     */
    public function __construct(
        Context           $context,
        CollectionFactory $collectionFactory,
        UserFactory $userFactory,
        UserResourceModel $resourceModel,
        SessionFactory $customerSessionFactory,
        array             $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->userFactory = $userFactory;
        $this->resourceModel = $resourceModel;
        $this->customerSessionFactory = $customerSessionFactory;
    }

    /**
     * Gets only active feedbacks
     *
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collectionFactory->create()
            ->addFieldToFilter(FeedbackInterface::IS_ACTIVE, 1)
            ->addFieldToFilter(FeedbackInterface::CUSTOMER_ID, $this->getLoggedCustomerId())
            ->setOrder(FeedbackInterface::CREATION_TIME, 'DESC');
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()
            ->createBlock(CustomPager::class,'feedback.list.pager')
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml(): string
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return mixed
     */
    public function getLoggedCustomerId()
    {
        $customerSession = $this->customerSessionFactory->create();
        $customer = $customerSession->getCustomer();
        return $customer->getId();
    }
}
