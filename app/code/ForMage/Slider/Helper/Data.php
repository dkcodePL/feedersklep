<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    CONST GROUPS_COUNT = 'slider/groups/count';
    CONST GROUPS_NAMES = 'slider/groups/name';

    CONST POSITION_COUNT = 50;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig
     */
    protected $_scopeConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfigValue($path, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->_scopeConfig->getValue($path, $scope);
    }

    public function getSlideGroups()
    {

        $names = $this->getGroupsNameConfig();

        $data = [];
        $max = (int)$this->getConfigValue(self::GROUPS_COUNT);
        foreach (range(1, $max) as $value) {
            $data[$value] = $names[$value] ?? __('Group %1', $value);
        }
        return $data;
    }

    public function getSlidePositions()
    {
        $data = [];
        $max = self::POSITION_COUNT;
        foreach (range(1, $max) as $value) {
            $data[$value] = __('Position %1', $value);
        }
        return $data;
    }

    public function getGroupsNameConfig()
    {
        $data = [];
        $groups = $this->getConfigValue(
            self::GROUPS_NAMES
        );
        $decoded = json_decode($groups, true);
        if ($decoded && is_array($decoded)) {
            foreach ($decoded as $row) {
                $data[$row['group_id']] = $row['name'];
            }
        }
        return $data;
    }


}
