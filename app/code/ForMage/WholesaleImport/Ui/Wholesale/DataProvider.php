<?php

namespace ForMage\WholesaleImport\Ui\Wholesale;

use ForMage\WholesaleImport\Model\ResourceModel\Wholesale\CollectionFactory;
use Magento\Framework\Registry;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var ForMage\WholesaleImport\Model\ResourceModel\Wholesale\CollectionFactory
     */
    protected $collection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;


    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Registry $coreRegistry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        Registry $coreRegistry,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $collectionFactory->create();

        $wholesale = $coreRegistry->registry('current_wholesale');

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);


        $this->meta = array_replace_recursive(
            $this->meta,
            $wholesale->getWholeSale()->getMeta()
        );

    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        foreach ($this->collection->getItems() as $item) {

            $this->_loadedData[$item->getId()] = $item->getData();
            $this->_loadedData[$item->getId()]['categories'] = $this->getSortedData(json_decode($item->getCategories(), true), ['category', 'wholesale']);
            $this->_loadedData[$item->getId()]['attributes'] = $this->getSortedData(json_decode($item->getAttributes(), true), ['file_field', 'attribute']);
            $this->_loadedData[$item->getId()]['custom_attributes'] = $this->getSortedData(json_decode($item->getCustomAttributes(), true), ['value', 'attribute', 'fn']);

            $data = json_decode($item->getWholesaleData(), true);

            $this->_loadedData[$item->getId()] += is_array($data) ? $data : [];
        }

        return $this->_loadedData;
    }

    protected function getSortedData($items, $keys)
    {
        $data = [];
        foreach ($items ?? [] as $item) {
            foreach ($keys as $key) {
                $data[$item['position']][$key] = $item[$key] ?? '';
            }
        }

        return array_values($data);
    }
}