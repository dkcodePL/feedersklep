<?php

namespace ForMage\PSRestrictions\Plugin\Payment\Checks;

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