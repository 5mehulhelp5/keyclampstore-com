<?php

namespace Best4Mage\DPPC\Ui\Component\Listing\Column;

class Sides extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var \Best4Mage\DPPC\Model\SideFactory
     */
    protected $_sideFactory;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Best4Mage\DPPC\Model\SideFactory $sideFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_sideFactory = $sideFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $sideIds = explode(',', $item['side_id']);
                $sideCollection = $this->_sideFactory->create()->getCollection()->addFieldToSelect('title')->addFieldToFilter('code', ['in' => $sideIds]);
                $sides = [];
                foreach ($sideCollection as $side) {
                    $sides[] = $side->getTitle();
                }
                $sides = implode(', ', $sides);

                $item['side_id'] = $sides; //Here you can do anything with actual data
            }
        }
        return $dataSource;
    }
}
