<?php
declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Training\Feedback\Model\ResourceModel\RatingOption\CollectionFactory as RatingOptionCollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Escaper;

/**
 * Block for a Feedback's front-end form
 */
class FeedbackForm implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;
    
    /**
     * 
     * @var RatingOptionCollectionFactory
     */
    private RatingOptionCollectionFactory $ratingOptionCollectionFactory;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param UrlInterface $urlBuilder
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
         RatingOptionCollectionFactory $ratingOptionCollectionFactory,    
        LoggerInterface $logger,
        Escaper $escaper    
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->ratingOptionCollectionFactory = $ratingOptionCollectionFactory;
        $this->logger = $logger;
        $this->escaper = $escaper;
    }

    /**
     * Gets customer's name
     * 
     * @return string
     */
    public function getCustomerName(): string
    {
        try {            
            $customerName = $this->customerRepository
                    ->getById($this->getCustomerId())
                    ->getFirstname();
        } catch (\Exception $e) {
            $customerName = '';
            $this->logger->error($e->getLogMessage());
        }
        return $customerName;
    }
    
    /**
     * Gets customer's email
     * 
     * @return string
     */
    public function getCustomerEmail(): string
    {
        try {            
            $customerEmail = $this->customerRepository
                    ->getById($this->getCustomerId())
                    ->getEmail();
        } catch (\Exception $e) {
            $customerEmail = '';
            $this->logger->error($e->getLogMessage());
        }
        return $customerEmail;
    }
    
    /**
     * Gets customer's Id
     * 
     * @return int
     */
    public function getCustomerId(): int
    {
        return (int)$this->customerSession->getCustomer()->getId();       
    }

    /**
     * Get the action URL for the feedback form submission
     * 
     * @return string
     */
    public function getActionUrl(): string
    {
        return $this->urlBuilder->getUrl('training_feedback/index/save');
    }
    
    /**
     * 
     * @return type
     */
    public function getRatingOptions()
    {
        $collection = $this->ratingOptionCollectionFactory->create();
        return $collection->getItems();
    }
}
