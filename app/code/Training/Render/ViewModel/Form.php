<?php

namespace Training\Render\ViewModel;

use \Magento\Framework\UrlInterface;
use \Magento\Framework\View\Element\Block\ArgumentInterface;

class Form implements ArgumentInterface
{
    private $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder  =  $urlBuilder;
    }

    public function getSubmitUrl()
    {
        return $this->urlBuilder->getUrl('customer/account/login');
    }
}
