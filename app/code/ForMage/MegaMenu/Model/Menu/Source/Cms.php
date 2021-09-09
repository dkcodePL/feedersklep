<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Model\Menu\Source;

use Magento\Framework\Data\OptionSourceInterface;


class Cms implements OptionSourceInterface
{

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_search;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $_cmsPage;

    public function __construct(
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->_cmsPage = $pageRepository;
        $this->_search = $searchCriteriaBuilder;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $pages = [];
        foreach($this->_cmsPage->getList($this->_getSearchCriteria())->getItems() as $page) {
            $pages[] = [
                'value' => $page->getId(),
                'label' => $page->getTitle()
            ];
        }
        return $pages;
    }

    protected function _getSearchCriteria()
    {
        return $this->_search->addFilter('is_active', '1')->create();
    }
}
