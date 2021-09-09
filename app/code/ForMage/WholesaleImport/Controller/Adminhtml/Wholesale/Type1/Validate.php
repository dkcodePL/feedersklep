<?php

namespace ForMage\WholesaleImport\Controller\Adminhtml\Wholesale\Type1;

use Magento\Backend\App\Action;
use Magento\Framework\DataObject;
use Magento\Framework\Controller\ResultFactory;

class Validate extends Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;


    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     * @since 100.2.0
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * Validate constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Framework\Serialize\Serializer\Json::class
        );
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('ForMage_WholesaleImport::wholesale');
    }

    /**
     *
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $messages = [];
        $response = new \Magento\Framework\DataObject();

        try {

            $data = [];
            foreach ($params['dynamic_rows'] ?? [] as $key => $row) {

                if (in_array($row['wholesale'], $data)) {
                    $messages[] = __("Wholesale category can be assigned only once: %1", $row['wholesale']);
                } else {
                    $data[] = $row['wholesale'];
                }

            }


        } catch (\Exception $exception) {

            $messages[] = $exception->getMessage();

        }

        $response->setMessages($messages);
        $response->setError(!empty($messages));

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;

    }


}
