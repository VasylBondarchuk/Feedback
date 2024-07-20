<?php

namespace Training\Feedback\Block\Adminhtml\Feedback;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_objectId;
    /**
     * @var string
     */
    protected $_blockGroup;
    /**
     * @var string
     */
    protected $_controller;
    /**
     * @var Magento\Backend\Block\Widget\Button\ButtonList
     */
    protected $buttonList;
    /**
     * Dependency Initilization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'feedback_id';
        $this->_blockGroup = 'Training_Feedback';
        $this->_controller = 'adminhtml_feedback';
        $this->buttonList->remove('delete');
        parent::_construct();
    }
}