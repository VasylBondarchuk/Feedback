<?php
declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;


/**
 * View Model for a Feedback's front-end form
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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param UrlInterface $urlBuilder
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        LoggerInterface   $logger    
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

     /**
     * Gets customer's name
     * 
     */
    public function getCustomerName() : string
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
     */
    public function getCustomerEmail() : string
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
     */
    public function getCustomerId() : int
    {
        return (int)$this->customerSession->getCustomer()->getId();       
    }

    /**
     * @return string
     */
    public function getActionUrl(): string
    {
        return $this->urlBuilder->getUrl('training_feedback/index/save');
    }
}
