<?php
/**
 * Copyright &copy; Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Demo\PromotionalProducts\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 *
 * Adds action links (e.g., Edit) to the product listing grid.
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var string
     */
    protected $_viewUrl;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param string $viewUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        $viewUrl = '',
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_viewUrl = $viewUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * Adds the 'Edit' action link to the data source for each product item.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    // Add the 'Edit' link to each item in the grid
                    $item[$name]['view'] = [
                        'href' => $this->_urlBuilder->getUrl('grids/custom/edit', ['id' => $item['id']]),
                        'target' => '_blank',
                        'label' => __('Edit')
                    ];
                }
            }
        }
        return $dataSource;
    }
}
