<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Block\Adminhtml\Menu;

/**
 * Class Edit
 * @package ForMage\MegaMenu
 */
class Edit extends \Magento\Framework\View\Element\Template
{

    public function getRefreshPathUrl()
    {
        return $this->getUrl('megamenu/*/refreshPath', ['_current' => true]);
    }

}
