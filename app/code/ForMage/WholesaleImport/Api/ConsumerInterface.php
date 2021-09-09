<?php

namespace ForMage\WholesaleImport\Api;

use Magento\AsynchronousOperations\Api\Data\OperationInterface;

interface ConsumerInterface
{
    /**
     * @param OperationInterface $operation
     * @return void
     */
    public function processOperation(OperationInterface $operation);

}