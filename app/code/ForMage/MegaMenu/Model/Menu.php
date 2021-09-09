<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;

/**
 * Class Menu
 * @package ForMage\MegaMenu
 */
class Menu extends AbstractModel
{
    const CACHE_TAG = '4mage_menu';

    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var MenuFactory
     */
    protected $_menuFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ForMage\MegaMenu\Model\ResourceModel\Menu');
    }

    /**
     * Menu constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param MenuFactory $menuFactory
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Menu $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        MenuFactory $menuFactory,
        \Magento\Framework\Registry $registry,
        ResourceModel\Menu $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_menuFactory = $menuFactory;
        $this->_storeManager = $storeManager;

    }

    /**
     * @return bool
     */
    public function isMenu()
    {
        return (int)$this->getParentId() === (int)\Magento\Catalog\Model\Category::TREE_ROOT_ID;
    }

    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * @return bool
     */
    public function canShow()
    {
        if (in_array(Store::DEFAULT_STORE_ID, $this->getStoreIds())) {
            return true;
        }
        if (in_array($this->getStore()->getId(), $this->getStoreIds())) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->getData('is_active');
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @param null $storeId
     * @return mixed
     */
    public function addStoreFilter($collection, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->getStore()->getId();
        }

        $collection->addFieldToFilter('store_ids', [
            ['finset' => Store::DEFAULT_STORE_ID],
            ['finset' => $storeId]
        ]);

        return $collection;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        $values['stores'] = '1';
        $values['is_active'] = '1';

        return $values;
    }

    /**
     * get tree path ids
     *
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if ($ids === null) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }

        return $ids;
    }

    /**
     * get all parent ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), [$this->getId()]);
    }

    public function getParentCategories()
    {
        return $this->getResource()->getParentCategories($this->getParentIds());
    }

    public function move($parentId, $afterCategoryId)
    {

        try {
            $parent = $this->_menuFactory->create()->load($parentId);
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Sorry, but we can\'t find the new parent menu you selected.'
                ),
                $e
            );
        }

        if (!$this->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Sorry, but we can\'t find the new menu you selected.')
            );
        } elseif ($parent->getId() == $this->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'We can\'t move the menu because the parent menu name matches the child category name.'
                )
            );
        }

        /**
         * Setting affected category ids for third party engine index refresh
         */
        $this->setMovedCategoryId($this->getId());


        $this->_getResource()->beginTransaction();
        try {

            $this->getResource()->changeParent($this, $parent, $afterCategoryId);
            $this->_getResource()->commit();

        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }

        $this->_cacheManager->clean([self::CACHE_TAG]);

        return $this;
    }


}
