<?php

/**
 * 4mage.co Package
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the 4mage.co license that is
 * available through the world-wide-web at this URL:
 * https://4mage.co/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	ForMage
 * @package 	ForMage_Base
 * @copyright 	Copyright (c) 2020 4mage.co (https://4mage.co/)
 * @license  	https://4mage.co/license-agreement/
 */

namespace ForMage\Base\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class ImageUploader extends \Magento\Catalog\Model\ImageUploader
{

    const BREAKPOINT = 'b';
    const QUALITY = 'q';

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * ImageUploader constructor.
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param string $baseTmpPath
     * @param string $basePath
     * @param array $allowedExtensions
     */
    public function __construct(
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        $baseTmpPath,
        $basePath,
        $allowedExtensions
    )
    {
        parent::__construct($coreFileStorageDatabase, $filesystem, $uploaderFactory, $storeManager, $logger, $baseTmpPath, $basePath, $allowedExtensions);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
        $this->file = $file;
    }

    public function getImageUrl($file, $width = null, $quality = 100)
    {
        if ($width) {
            $this->resizeImage($file, $width, $quality);
        }

        return $this->storeManager
                ->getStore()
                ->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $this->getFilePath($width ? $this->getImagePathByWidth($width, $quality) : $this->getBasePath(), $file);
    }

    public function resizeImage($file, $width, $quality, $keepRatio = true)
    {
        $resizeImagePath = $this->getImagePathByWidth($width, $quality);
        $image = $resizeImagePath . '/' . $file;

        /** @var \Magento\Framework\Filesystem\Directory\WriteInterface $mediaDirectory */
        $mediaDirectory = $this->mediaDirectory;
        if ($mediaDirectory->isFile($image)) return false;

        $this->file->checkAndCreateFolder($mediaDirectory->getAbsolutePath($resizeImagePath));

        try {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($mediaDirectory->getAbsolutePath($this->getBasePath() . '/' . $file));
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio($keepRatio);
            $imageResize->resize($width, null);
            $imageResize->quality($quality);
            $imageResize->save($mediaDirectory->getAbsolutePath($image));

        } catch (\Exception $e) {


        }

    }

    public function getImagePath($file)
    {
        /** @var \Magento\Framework\Filesystem\Directory\WriteInterface $mediaDirectory */
        $mediaDirectory = $this->mediaDirectory;
        return $mediaDirectory->getAbsolutePath($this->getBasePath() . '/' . $file);
    }

    public function getImagePathByWidth($width, $quality)
    {
        return $this->getFilePath($this->getBasePath(), '/' . self::BREAKPOINT . '/' . $width. '/' . self::QUALITY . '/' . $quality);
    }

}
