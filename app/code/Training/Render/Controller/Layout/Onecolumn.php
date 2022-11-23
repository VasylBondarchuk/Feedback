<?php

namespace Training\Render\Controller\Layout;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Onecolumn implements HttpGetActionInterface
{

    /** @var PageFactory */
    private $pageFactory;

    public function __construct(
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {

        $page = $this->pageFactory->create();
        return $page;
    }
}

