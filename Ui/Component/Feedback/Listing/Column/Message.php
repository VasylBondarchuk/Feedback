<?php

namespace Training\Feedback\Ui\Component\Feedback\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Training\Feedback\Helper\FeedbackConfigProvider;

class Message extends Column
{
    private FeedbackConfigProvider $configProvider;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FeedbackConfigProvider $configProvider
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FeedbackConfigProvider $configProvider,
        array $components = [],
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['message'])) {
                    $item['message'] = $this->renderFeedbackText($item['message']);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Render feedback text with truncation
     *
     * @param string $feedbackText
     * @return string
     */
    public function renderFeedbackText(string $feedbackText): string
    {
        $length = (int) $this->configProvider->getDisplayLength();
        return mb_strlen($feedbackText) > $length
            ? mb_substr($feedbackText, 0, $length) . '...'
            : $feedbackText;
    }
}
