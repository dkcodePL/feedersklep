<?php

namespace ForMage\WholesaleImport\Controller\Adminhtml\Wholesale\Type1;

use Magento\Backend\App\Action;
use Magento\Framework\DataObject;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \ForMage\WholesaleImport\Model\WholesaleFactory
     */
    protected $_wholesaleFactory;

    /**
     * @var ResultFactory
     */
    protected $_resultFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var string[]
     */
    protected $_data = ['stock', 'set', 'default_categories', 'price', 'images', 'pre_sku', 'sku_source', 'store', 'url_key'];

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param ResultFactory $resultFactory
     * @param \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        ResultFactory $resultFactory,
        \ForMage\WholesaleImport\Model\WholesaleFactory $wholesale,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Framework\Serialize\Serializer\Json::class
        );
        parent::__construct($context);
        $this->_messageManager = $context->getMessageManager();
        $this->_resultFactory = $resultFactory;
        $this->_wholesaleFactory = $wholesale;
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

        try {

            $wholesale = $this->_wholesaleFactory->create()->load($params['id']);

            $categories = [];
            $temp = [];
            foreach ($params['categories'] ?? [] as $key => $row) {

                $position = isset($row['position']) ? $row['position'] : $key+1;

                $temp['category'] = $row['category'];
                $temp['wholesale'] = $row['wholesale'];
                $temp['position'] = $position;

                $categories[] = $temp;
            }
            $attributes = [];
            $temp = [];
            foreach ($params['attributes'] ?? [] as $key => $row) {

                $position = isset($row['position']) ? $row['position'] : $key+1;

                if (strlen($row['file_field']) < 1) continue;

                $temp['file_field'] = $row['file_field'];
                $temp['attribute'] = $row['attribute'];
                $temp['position'] = $position;

                $attributes[] = $temp;
            }

            $attributes2 = [];
            $temp = [];
            foreach ($params['custom_attributes'] ?? [] as $key => $row) {

                $position = isset($row['position']) ? $row['position'] : $key+1;

                $temp['attribute'] = $row['attribute'];
                $temp['value'] = $row['value'];
                $temp['fn'] = $row['fn'] ?? '';
                $temp['position'] = $position;

                $attributes2[] = $temp;
            }


            foreach ($this->_data as $dataItem) {
                $data[$dataItem] = $params[$dataItem] ?? '';
            }

            foreach ($params as $param => $value) {
                if (substr( $param, 0, 2 ) === "e_") {
                    $data[$param] = $value;
                }
            }

            $wholesale->setIsActive($params['is_active']);
            $wholesale->setUpdateProducts($params['update_products']);
            $wholesale->setUpdateStocks($params['update_stocks']);
            $wholesale->setUpdatePrices($params['update_prices']);
            $wholesale->setWholesaleName($params['wholesale_name']);
            $wholesale->setWholesaleAttributeId($params['wholesale_attribute_id']);
            $wholesale->setWholesaleData(json_encode($data));
            $wholesale->setCategories(json_encode($categories));
            $wholesale->setAttributes(json_encode($attributes));
            $wholesale->setCustomAttributes(json_encode($attributes2));
            $wholesale->save();

            $this->_messageManager->addSuccess(__('Saved'));

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/wholesale/index', ['type' => 'data']);
                return $this;
            }

        } catch (\Exception $exception) {

            $this->_messageManager->addError($exception->getMessage());
        }



        $this->_redirect('*/wholesale/index', ['type' => 'data']);

    }


}
