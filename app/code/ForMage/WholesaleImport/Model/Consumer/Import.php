<?php

namespace ForMage\WholesaleImport\Model\Consumer;

use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Framework\DB\Adapter\ConnectionException;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\TemporaryStateExceptionInterface;
use Magento\Framework\EntityManager\EntityManager;
use ForMage\WholesaleImport\Api\ConsumerInterface;
use ForMage\WholesaleImport\Model\Task;


class Import implements ConsumerInterface
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Magento\AsynchronousOperations\Model\OperationManagement
     */
    private $operationManagement;

    /**
     * @var EntityManager
     */
    private $entityManager;

    private $task;


    /**
     * Import constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Bulk\OperationManagementInterface $operationManagement
     * @param EntityManager $entityManager
     * @param Task $task
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Bulk\OperationManagementInterface $operationManagement,
        EntityManager $entityManager,
        Task $task
    )
    {
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->operationManagement = $operationManagement;
        $this->entityManager = $entityManager;
        $this->task = $task;
    }

    /**
     * @param OperationInterface $operation
     */
    public function processOperation(OperationInterface $operation)
    {

        $serializedData = $operation->getSerializedData();
        $unserializedData = $this->jsonHelper->jsonDecode($serializedData);

        $task = $this->task->load($unserializedData['entity_id']);

        try {






        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
            $status = ($e instanceof TemporaryStateExceptionInterface) ? OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED : OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();

            $message = $e->getMessage();
            unset($unserializedData['entity_link']);
            $serializedData = $this->jsonHelper->jsonEncode($unserializedData);
        } catch (LocalizedException $e) {
            $this->logger->critical($e->getMessage());
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
            $errorCode = $e->getCode();
            $message = $e->getMessage();

        } catch (\Exception $e) {

            $errorCode = $e->getCode();
            $message = __($e->getMessage());
            $status = OperationInterface::STATUS_TYPE_NOT_RETRIABLY_FAILED;
        }


        $operation->setStatus($status ?? OperationInterface::STATUS_TYPE_COMPLETE)
            ->setErrorCode($errorCode ?? null)
            ->setSerializedData($serializedData)
            ->setResultMessage($message ?? null);

        $this->entityManager->save($operation);

    }


}
