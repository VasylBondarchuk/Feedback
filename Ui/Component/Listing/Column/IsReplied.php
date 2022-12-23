<?php

namespace Training\Feedback\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Training\Feedback\Model\ReplyRepository;

class IsReplied extends Column
{
    private ReplyRepository $replyRepository;

    /**
     * @param ReplyRepository $replyRepository
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ReplyRepository $replyRepository,
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        array              $components = [],
        array              $data = []
    ) {
        $this->replyRepository = $replyRepository;
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
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['feedback_id'])) {
                    $item['is_replied'] = $this->replyRepository->isFeedbackReplied($item['feedback_id'])
                        ? "Yes"
                        : "No";
                }
            }
            return $dataSource;
        }
    }
}
