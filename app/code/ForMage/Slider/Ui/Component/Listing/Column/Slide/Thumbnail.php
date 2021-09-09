<?php

namespace ForMage\Slider\Ui\Component\Listing\Column\Slide;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Thumbnail
 */
class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const NAME = 'thumbnail';

    const ALT_FIELD = 'name';

    /**
     * @var \ForMage\Slider\Model\SlideFactory
     */
    protected $_slideFactory;

    /**
     * Thumbnail constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \ForMage\Slider\Model\SlideFactory $slide
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \ForMage\Slider\Model\SlideFactory $slide,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_slideFactory = $slide;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {

           $imageUploader =  $this->_slideFactory
                ->create()->getImageUploader();


            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $slide = new \Magento\Framework\DataObject($item);

                $item[$fieldName . '_src'] = $imageUploader->getImageUrl($slide->getImage(), 150);
                $item[$fieldName . '_alt'] = $slide->getName();
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'slider/slide/edit',
                    ['slide_id' => $slide->getSlideId()]
                );

                $item[$fieldName . '_orig_src'] = $imageUploader->getImageUrl($slide->getImage());
            }
        }

        return $dataSource;
    }

    /**
     * Get Alt
     *
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return $row[$altField] ?? null;
    }
}