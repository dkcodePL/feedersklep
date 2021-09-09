<?php

namespace ForMage\WholesaleImport\Ui\Task;

use ForMage\WholesaleImport\Model\ResourceModel\Task\CollectionFactory;
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
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $collectionFactory->create();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->collection->getSelect()->joinLeft(
            ['wholesale' => $this->collection->getTable('4mage_wholesaleimport_wholesale')],
            'main_table.wholesale_id = wholesale.id',
            ['wholesale_name']
        );

        $items = $this->collection->getItems();

        $data = [
            'totalRecords' => $this->collection->getSize(),
            'items' => [],
        ];

        foreach ($items as $item) {

            $item = $item->toArray([]);
            $item['short_messages'] = substr($item['messages'], 0, 100);

            $data['items'][] = $item;
        }

        return $data;
    }


}