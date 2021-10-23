<?php

namespace Training\Feedback\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Form implements HttpGetActionInterface
{
    private $pageFactory;

    public function __construct(
        PageFactory $pageFactory

    ) {
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        $page = $this->pageFactory->create();
        $page ->getConfig()->getTitle()->prepend(__('Feedback form'));
        return $page;
    }
}
