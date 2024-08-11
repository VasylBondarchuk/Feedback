<?php

namespace Training\Feedback\Block\Adminhtml\Feedback\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Training\Feedback\Model\DataProvider;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;

class Form extends Generic {

    protected DataProvider $dataProvider;
    protected Store $systemStore;

    public function __construct(
            Context $context,
            Registry $registry,
            FormFactory $formFactory,
            DataProvider $dataProvider,
            Store $systemStore, 
            array $data = []
    ) {
        $this->dataProvider = $dataProvider;
        $this->systemStore = $systemStore; 
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form data
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm() {
        $feedbackId = (int)$this->getRequest()->getParam('feedback_id');
        $data = $this->dataProvider->getData($feedbackId);
        $form = $this->_formFactory->create(
                ['data' => [
                        'id' => 'edit_form',
                        'enctype' => 'multipart/form-data',
                        'action' => $this->getData('action'),
                        'method' => 'post'
                    ]
                ]
        );
        $form->setHtmlIdPrefix('feedback_');

        $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Feedback Data'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
                'feedback_id',
                'hidden',
                ['name' => 'feedback_id', 'value' => $feedbackId]
        );

        $fieldset->addField(
                'is_active',
                'select',
                [
                    'name' => 'is_active',
                    'label' => __('Published'),
                    'options' => [1 => __('Yes'), 0 => __('No')],
                    'id' => 'is_anonymous',
                    'title' => __('Published'),
                    'class' => 'required-entry',
                    'required' => true,
                    'value' => isset($data[$feedbackId]['is_active']) ? $data[$feedbackId]['is_active'] : 0
                ]
        );

        // Add the store view select field
        $fieldset->addField(
                'store_id',
                'select',
                [
                    'name' => 'store_id',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, false),
                    'value' => isset($data[$feedbackId]['store_id']) ? $data[$feedbackId]['store_id'] : 0
                ]
        );
        
        
        // Add the custom ratings field
        $fieldset->addField(
                'ratings',
                'note',
                [
                    'label' => __('Ratings'),
                    'title' => __('Ratings'),
                    'text' => $this->getLayout()
                            ->createBlock('Training\Feedback\Block\Ratings')
                            ->setTemplate('Training_Feedback::common/form_ratings.phtml')
                            ->toHtml()
                ]
        );

        $fieldset->addField(
                'is_anonymous',
                'select',
                [
                    'name' => 'is_anonymous',
                    'label' => __('Anonymous'),
                    'options' => [1 => __('Yes'), 0 => __('No')],
                    'id' => 'is_anonymous',
                    'title' => __('Anonymous'),
                    'class' => 'required-entry',
                    'required' => true,
                    'disabled' => true,
                    'value' => isset($data[$feedbackId]['is_anonymous']) ? $data[$feedbackId]['is_anonymous'] : 0
                ]
        );

        $fieldset->addField(
                'author_name',
                'text',
                [
                    'name' => 'author_name',
                    'label' => __('Author Name'),
                    'title' => __('Author Name'),
                    'required' => true,
                    'value' => isset($data[$feedbackId]['author_name']) ? $data[$feedbackId]['author_name'] : ''
                ]
        );

        $fieldset->addField(
                'author_email',
                'text',
                [
                    'name' => 'author_email',
                    'label' => __('Author Email'),
                    'title' => __('Author Email'),
                    'required' => true,
                    'value' => isset($data[$feedbackId]['author_email']) ? $data[$feedbackId]['author_email'] : ''
                ]
        );

        $fieldset->addField(
                'message',
                'textarea',
                [
                    'name' => 'message',
                    'label' => __('Message'),
                    'title' => __('Message'),
                    'required' => true,
                    'value' => isset($data[$feedbackId]['message']) ? $data[$feedbackId]['message'] : ''
                ]
        );

        $fieldset->addField(
                'reply_notification',
                'select',
                [
                    'name' => 'reply_notification',
                    'label' => __('Notify about reply'),
                    'options' => [1 => __('Yes'), 0 => __('No')],
                    'id' => 'reply_notification',
                    'title' => __('Notify about reply'),
                    'class' => 'required-entry',
                    'required' => true,
                    'value' => isset($data[$feedbackId]['reply_notification']) ? $data[$feedbackId]['reply_notification'] : 0
                ]
        );

        $fieldset->addField(
                'is_replied',
                'select',
                [
                    'name' => 'is_replied',
                    'label' => __('Replied'),
                    'options' => [1 => __('Yes'), 0 => __('No')],
                    'id' => 'is_replied',
                    'title' => __('Replied'),
                    'class' => 'required-entry',
                    'required' => true,
                    'disabled' => true,
                    'value' => isset($data[$feedbackId]['is_replied']) ? $data[$feedbackId]['is_replied'] : 0
                ]
        );

        $fieldset->addField(
                'reply_text',
                'textarea',
                [
                    'name' => 'reply_text',
                    'label' => __('Reply'),
                    'title' => __('Reply'),
                    'required' => false,
                    'value' => isset($data[$feedbackId]['reply_text']) ? $data[$feedbackId]['reply_text'] : ''
                ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
