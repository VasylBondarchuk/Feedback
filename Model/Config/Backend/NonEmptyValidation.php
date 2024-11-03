<?php

namespace Training\Feedback\Model\Config\Backend;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;
use Magento\Config\Model\Config\Structure;

class NonEmptyValidation extends Value
{
    /**
     * @var Structure
     */
    protected $configStructure;

    /**
     * Constructor
     *
     * @param Structure $configStructure
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfigInterface $scopeConfig,
        Structure $configStructure,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        array $data = []
    ) {
        $this->configStructure = $configStructure;
        parent::__construct($context, $registry, $scopeConfig, $cacheTypeList, $cacheFrontendPool, $data);
    }

    /**
     * Validate non-empty values for all fields in the group before saving
     *
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $sectionId = $this->getData('section');
        $groupId = $this->getData('group_id');

        if (!$sectionId || !$groupId) {
            parent::beforeSave();
            return;
        }

        // Retrieve fields for the section/group
        $fields = $this->configStructure->getElementByPath("$sectionId/$groupId")->getChildren();

        foreach ($fields as $fieldId => $field) {
            $value = $this->getData($fieldId);
            if ($value === null || $value === '') {
                throw new ValidatorException(__("The field '%1' cannot be empty.", $field->getLabel()));
            }
        }

        parent::beforeSave();
    }
}
