<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Ui\Component;

use ForMage\MegaMenu\Model\ResourceModel\Menu\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var \ForMage\MegaMenu\Model\ResourceModel\Menu\Collection
     */
    protected $collection;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->meta['menu']['children'] = [
            'parent' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'hidden',
                            'componentType' => 'field',
                            'dataScope' => 'parent',
                            'initialValue' => true,
                            'value' => $request->getParam('parent', 0)
                        ]
                    ]
                ]
            ]
        ];
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
        $items = $this->collection->getItems();
        foreach ($items as $item) {

            $data = $item->getData();
            $data['parent'] = $data['parent_id'];
            $data['categories'] = $data['type_id'];


            $this->_loadedData[$item->getId()] = $data;
        }
        return $this->_loadedData;
    }


}