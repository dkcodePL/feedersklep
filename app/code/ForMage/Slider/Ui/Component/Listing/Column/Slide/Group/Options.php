<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Ui\Component\Listing\Column\Slide\Group;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var \ForMage\Slider\Helper\Data
     */
    protected $_helper;

    /**
     * Options constructor.
     * @param \ForMage\Slider\Helper\Data $helper
     */
    public function __construct(
        \ForMage\Slider\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
    }


    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        foreach ($this->_helper->getSlideGroups() as $value => $label) {
            $this->options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $this->options;
    }
}
