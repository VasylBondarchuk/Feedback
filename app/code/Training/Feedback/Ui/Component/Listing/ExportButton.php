<?php
declare(strict_types=1);

namespace Training\Feedback\Ui\Component\Listing;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\ExportButton as ExportButtonsDefault;

/**
 * Provides data regarding Export button
 */
class ExportButton extends ExportButtonsDefault
{
    /** Component name */
    public const NAME = 'exportButton';

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UrlInterface     $urlBuilder
     * @param array            $components
     * @param array            $data
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $urlBuilder, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName(): string
    {
        return static::NAME;
    }

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        unset($config['options']['csv'], $config['options']['xml']);
        $this->setData('config', $config);
        parent::prepare();
    }
}
