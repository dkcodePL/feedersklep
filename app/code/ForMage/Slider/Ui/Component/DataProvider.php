<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\Slider\Ui\Component;

use ForMage\Slider\Model\ResourceModel\Slide\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const PATH = 'slide';

    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var \ForMage\Slider\Model\ResourceModel\Slide\CollectionFactory
     */
    protected $collection;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var \Magento\Framework\File\Mime
     */
    private $mime;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\Mime $mime
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Mime $mime,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->mime = $mime;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        foreach ($items as $slide) {

            $data = $slide->getData();
            if ($data['image'] && $this->isExist($data['image'])) {
                $data['image'] = $this->getImageData($data['image']);
                $data['image'][0]['url'] = $slide->getImageUploader()->getImageUrl($slide->getImage());
            }
            $this->_loadedData[$slide->getId()] = $data;
        }
        return $this->_loadedData;
    }

    /**
     * @param string $file
     * @return array
     */
    protected function getImageData($file)
    {
        $stat = $this->getStat($file);
        return [
            [
                'file' => $file,
                'size' => isset($stat) ? $stat['size'] : 0,
                'name' => basename($file),
                'type' => $this->getMimeType($file),
            ]
        ];
    }

    /**
     * Get file statistics data
     *
     * @param string $fileName
     * @return array
     */
    public function getStat($fileName)
    {
        $filePath = self::PATH . '/' . ltrim($fileName, '/');

        $result = $this->mediaDirectory->stat($filePath);
        return $result;
    }

    /**
     * Check if the file exists
     *
     * @param string $fileName
     * @return bool
     */
    public function isExist($fileName)
    {
        $filePath = self::PATH . '/'  . ltrim($fileName, '/');

        $result = $this->mediaDirectory->isExist($filePath);
        return $result;
    }

    /**
     * Retrieve MIME type of requested file
     *
     * @param string $fileName
     * @return string
     */
    public function getMimeType($fileName)
    {
        $filePath = self::PATH . '/'  . ltrim($fileName, '/');
        $absoluteFilePath = $this->mediaDirectory->getAbsolutePath($filePath);

        $result = $this->mime->getMimeType($absoluteFilePath);
        return $result;
    }


}