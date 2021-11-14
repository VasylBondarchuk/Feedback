<?php

namespace Training\FeedbackProduct\Plugin\Model;
use Training\Feedback\Api\Data\FeedbackExtensionInterface;
use Training\Feedback\Api\Data\FeedbackExtensionInterfaceFactory;
use Training\Feedback\Api\Data\FeedbackInterface;

class FeedbackExtension
{
    private $extensionAttributesFactory;
    public function __construct(
        FeedbackExtensionInterfaceFactory $extensionAttributesFactory
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }
    public function afterGetExtensionAttributes(FeedbackInterface $subject,$result)
    {
        if (!is_null($result)) {
            return $result;
        }
        /** @var FeedbackExtensionInterface $extensionAttributes */
        $extensionAttributes = $this->extensionAttributesFactory->create();
        $subject->setExtensionAttributes($extensionAttributes);
        return $extensionAttributes;
    }
}
