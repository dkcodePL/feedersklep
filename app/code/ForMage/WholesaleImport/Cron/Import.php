<?php

namespace ForMage\WholesaleImport\Cron;

use ForMage\WholesaleImport\Model\ResourceModel\Wholesale\Collection;
use ForMage\WholesaleImport\Helper\Data as Helper;
use ForMage\WholesaleImport\Model\{
    ScheduleBulkFactory, TaskFactory
};

class Import
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_collection;

    /**
     * @var ScheduleBulkFactory
     */
    protected $_scheduleBulk;

    /**
     * @var \ForMage\WholesaleImport\Helper\Data
     */
    protected $_helper;

    /**
     * @var \ForMage\WholesaleImport\Model\TaskFactory
     */
    protected $_task;

    /**
     * Import constructor.
     * @param Collection $collection
     * @param TaskFactory $task
     * @param ScheduleBulkFactory $scheduleBulk
     * @param Helper $helper
     */
    public function __construct(
        Collection $collection,
        TaskFactory $task,
        ScheduleBulkFactory $scheduleBulk,
        Helper $helper
    )
    {
        $this->_scheduleBulk = $scheduleBulk;
        $this->_task = $task;
        $this->_helper = $helper;
        $this->_collection = $collection;
        $this->_collection->isActive();
    }

    public function products()
    {
        $collection = $this->_collection
            ->addFieldToFilter('update_products', 1);

        foreach ($collection as $wholesale) {

//                foreach (array_chunk($wholesale->getWholeSale()->getProducts(true), 10) as $chunk) {
//
//                }

        }

    }

    public function stocks()
    {
        $collection = $this->_collection
            ->addFieldToFilter('update_stocks', 1);

        foreach ($collection as $item) {

            $wholesale = $item->getWholesale();

            $_stock = $wholesale->getStock();
            foreach (array_chunk($_stock, 500) as $chunk) {

                try {
                    $this->_helper->importStocks($chunk);
                } catch (\Exception $exception) {
                    echo $exception->getMessage();
                }
            }
        }
    }

    public function prices()
    {
        $collection = $this->_collection
            ->addFieldToFilter('update_prices', 1);

        foreach ($collection as $item) {
            $wholesale = $item->getWholeSale();

            foreach (array_chunk($wholesale->getPrices(), 500, true) as $chunk) {

                $this->_helper->updatePrices($chunk);
            }



        }

    }


}
