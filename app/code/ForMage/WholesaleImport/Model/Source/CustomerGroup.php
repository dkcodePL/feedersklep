<?php

namespace ForMage\WholesaleImport\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Api\Data\GroupSearchResultsInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class CustomerGroup implements OptionSourceInterface
{

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Group constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    )
    {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray() {

        $options = [];
        $options[] = [
            'label' => '---',
            'value' => ''
        ];

        foreach ($this->getGroups() as $group) {

            $options[] = [
                'label' => $group->getCode() . ' (ID: ' . $group->getId() . ')',
                'value' => $group->getId()
            ];
        }

        return $options;
    }

    protected function getGroups()
    {
        /** @var GroupSearchResultsInterface $groups */
        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create());

        return $groups->getItems();
    }
}