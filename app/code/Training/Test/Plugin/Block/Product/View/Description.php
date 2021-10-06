<?php

namespace Training\Test\Plugin\Block\Product\View;

/**
 * Customizes the Catalog\Block\Product\View\Description block and assign description.phtml
template to it
 */
class Description extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Catalog\Block\Product\View\Description $subject
     */
    public function beforeToHtml(\Magento\Catalog\Block\Product\View\Description $subject)
    {
        //$subject->getProduct()->setDescription('Test description');
       $subject->setTemplate('Training_Test::description.phtml');
    }
}
