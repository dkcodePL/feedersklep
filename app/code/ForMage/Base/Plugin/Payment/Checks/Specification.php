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

namespace ForMage\Base\Plugin\Payment\Checks;

use Magento\Payment\Model\Checks\SpecificationFactory;


class Specification
{
    /**
     * @var array
     */
    private $additionalChecks;

    /**
     * Specification constructor.
     * @param array $additionalChecks
     */
    public function __construct(array $additionalChecks = [])
    {
        $this->additionalChecks = $additionalChecks;
    }

    /**
     * @param SpecificationFactory $subject
     * @param array $checks
     *
     * @return array
     */
    public function beforeCreate(SpecificationFactory $subject, array $checks): array
    {
        $checks = array_merge(
            $checks,
            $this->additionalChecks
        );
        return [$checks];
    }
}