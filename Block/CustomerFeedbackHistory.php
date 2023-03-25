<?php
declare(strict_types=1);

namespace Training\Feedback\Block;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\User\Model\ResourceModel\User as UserResourceModel;
use Magento\User\Model\UserFactory;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Model\ResourceModel\Feedback\Collection;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 *
 */
class CustomerFeedbackHistory extends Template
{
    const DEFAULT_SORT_ORDER = 'desc';
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
     * 
     * @var RequestInterface
    */
    private RequestInterface $request;

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
        RequestInterface $request,    
        array             $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->userFactory = $userFactory;
        $this->resourceModel = $resourceModel;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->request = $request;
    }

    /**
     * Gets only active feedbacks
     *
     * @return Collection
     */
    public function getCollection(): Collection
    {
        $collection = $this->collectionFactory->create();
        
        // Pagination
        $pageNum = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;        
        $collection->setPageSize($pageSize)->setCurPage($pageNum);        
        
        // Filtering
        $collection->addFieldToFilter(FeedbackInterface::CUSTOMER_ID, $this->getLoggedCustomerId());        
        // Sorting
        $collection->setOrder(FeedbackInterface::CREATION_TIME, $this->getCurrentDirection());        
        
        return $collection;            
    }   
   

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()
            ->createBlock(CustomPager::class, 'feedback.list.pager')
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        return $this;
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
    
    // Returns sorting order, selected by front-end user  
    public function getCurrentDirection()  {
        return ($this->request->getParam('order')) ?? self::DEFAULT_SORT_ORDER;
                
    }
    
    // Provides options vaules and options labels to the sorting order select 
    public function getAvailableOrders() : array{
        return [
            'desc' => 'From newest to oldest',
            'asc' => 'From oldest to newest',
            ];
    }
    
    // Checks if selected sorting order corresponds to the current one 
    public function isOrderCurrent(string $order) : bool{
        return $order === $this->getCurrentDirection();
    }
}
