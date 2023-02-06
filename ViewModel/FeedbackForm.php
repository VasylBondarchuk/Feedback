<?php
declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 *
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
     * @param UrlInterface $urlBuilder
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     */
    public function getCustomerName() : string
    {
        try {
            $customerId =  (int)$this->customerSession->getCustomer()->getId();
            $customerName = $this->customerRepository->getById($customerId)->getFirstname();
        } catch (\Exception $exception) {
            $customerName = '';
        }
        return $customerName;
    }
    
    /**
     */
    public function getCustomerEmail() : string
    {
        try {
            $customerId =  (int)$this->customerSession->getCustomer()->getId();
            $customerEmail = $this->customerRepository->getById($customerId)->getEmail();
        } catch (\Exception $exception) {
            $customerEmail = '';
        }
        return $customerEmail;
    }

    /**
     * @return string
     */
    public function getActionUrl(): string
    {
        return $this->urlBuilder->getUrl('training_feedback/index/save');
    }
}
