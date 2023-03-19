<?php
declare(strict_types=1);

namespace Training\Feedback\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;

/**
 *
 */
class FeedbackSorter extends Template
{    
    const DEFAULT_SORT_ORDER = 'desc';   
    const FILTERING_PARAM_REQUEST_NAME = 'filtering_param'; 
    
    /**
     * 
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * 
     * @param Context $context
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        Context           $context,        
        RequestInterface $request,    
        array $data = []
    ) {
        parent::__construct($context, $data);        
        $this->request = $request;
    }
    
    // Returns sorting order, selected by front-end user  
    public function getCurrentDirection()  {
        return ($this->request->getParam('order')) ?? self::DEFAULT_SORT_ORDER;
                
    }
    
    // Returns sorting order, selected by front-end user  
    public function getCurrentFilteringParam()  {
        return ($this->request->getParam(self::FILTERING_PARAM_REQUEST_NAME))
        ?? self::DEFAULT_FILTERING_PARAM;
                
    }
    
    // Provides options vaules and options labels to the sorting order select 
    public function getAvailableSortingOrders() : array{
        return [
            'desc' => 'From newest to oldest',
            'asc' => 'From oldest to newest',
            ];
    }
    
    // Checks if selected sorting order corresponds to the current one 
    public function isOrderCurrent(string $order) : bool{
        return $order === $this->getCurrentDirection();
    }    
   
    // Checks if selected sorting order corresponds to the current one 
    public function isFilteringApplied() : bool{
        return (bool)$this->request->getParam(self::FILTERING_PARAM_REQUEST_NAME);
    }
    
    // Returns request string
    public function getUrlRequest(){
        return $this->isFilteringApplied()
        ? self::FILTERING_PARAM_REQUEST_NAME . "={$this->getCurrentFilteringParam()}&"
        : "";
    }
}
