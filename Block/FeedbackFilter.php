<?php
declare(strict_types=1);

namespace Training\Feedback\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;

/**
 *
 */
class FeedbackFilter extends Template
{    
    
    const DEFAULT_FILTERING_PARAM = 'all';
    const FILTERING_PARAM_REQUEST_NAME = 'filtering_param'; 
    
    /**
     * 
     * @var RequestInterface
     */
    private RequestInterface $request;
   
    public function __construct(
        Context           $context,        
        RequestInterface $request,    
        array $data = []
    ) {
        parent::__construct($context, $data);        
        $this->request = $request;
    }
        
    // Returns sorting order, selected by front-end user  
    public function getCurrentFilteringParam()  {
        return ($this->request->getParam(self::FILTERING_PARAM_REQUEST_NAME))
        ?? self::DEFAULT_FILTERING_PARAM;
                
    }    
   
    
    // Provides options vaules and options labels to the sorting order select 
    public function getFilteringParam() : array{
        return [
            'all' => __('All Feedbacks'),
            'registered' => __('Only from registered customers')            
            ];
    }    
    
    // Checks if selected sorting order corresponds to the current one 
    public function isFilteringParamCurrent(string $order) : bool{
        return $order === $this->getCurrentFilteringParam();
    }
    
}
