<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use ForMage\Slider\Model\SlideFactory;

class Slider extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/slider.phtml";

    protected $_storeManager;

    protected $_breakpoints = [320, 480, 640, 768, 1024, 1280, 1440, 1920, 2560];

    protected $_allowedTags = '<p><h1><h2><h3><br><ol><ul><li><i><b><em><div><span><a>';

    /**
     * @var \ForMage\Slider\Model\SlideFactory
     */
    protected $_slideFactory;

    /**
     * Slider constructor.
     * @param Template\Context $context
     * @param SlideFactory $slide
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SlideFactory $slide,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_slideFactory = $slide;
        $this->_storeManager = $context->getStoreManager();
    }

    public function getModel()
    {
        return $this->_slideFactory
            ->create();
    }

    public function getImageUrl($slide, $width = null, $quality = 100)
    {
        return $this->getModel()->getImageUploader()->getImageUrl($slide->getImage(), $width, $quality);
    }

    public function getSlides()
    {
        return $this->getModel()
            ->getCollection()
            ->getByGroup($this->getData('group'), $this->_storeManager->getStore()->getId());
    }

    public function getSrcSet($slide)
    {
        $imageUploader = $this->getModel()->getImageUploader();

        $images = [];
        foreach ($this->_breakpoints as $breakpoint) {
              $images[] = $imageUploader->getImageUrl($slide->getImage(), $breakpoint) . ' ' . $breakpoint . 'w';
        }

        return implode(', ', $images);
    }

    public function getContent($item)
    {
        return strip_tags($item->getHtml(), $this->_allowedTags);
    }


}