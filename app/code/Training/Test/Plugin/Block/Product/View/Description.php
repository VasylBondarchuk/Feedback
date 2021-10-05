<?php

namespace Training\Test\Plugin\Block\Product\View;

/**
 * Adds 'Test description' to a description of a product
 */
class Description extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Catalog\Block\Product\View\Description $subject
     */
    public function beforeToHtml(\Magento\Catalog\Block\Product\View\Description $subject)
    {
        $subject->getProduct()->setDescription('Test description');
    }
}
