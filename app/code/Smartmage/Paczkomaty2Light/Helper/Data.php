<?php

namespace Smartmage\Paczkomaty2Light\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Smartmage\Paczkomaty2Light\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const apiUrl       = 'https://api-shipx-pl.easypack24.net/';
    const testApiUrl   = 'https://sandbox-api-shipx-pl.easypack24.net/';
    const versionParam = 'v1';

    /**
     * @var mixed
     */
    protected $orgId;
    /**
     * @var
     */
    protected $curl;
    /**
     * @var string
     */
    protected $curlUrl;
    /**
     * @var bool
     */
    protected $isTestMode = false;

    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->orgId = $this->getConfig('carriers/smpaczkomaty2/orgid');
        $this->isTestMode = $this->getConfig('carriers/smpaczkomaty2/testmode');
        $this->curlUrl = ($this->getIsTestMode()) ? self::testApiUrl : self::apiUrl;
        $this->curlUrl .= self::versionParam . '/';
    }

    /**
     * @param $config_path
     * @return mixed
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function getIsTestMode()
    {
        return ($this->isTestMode);
    }

    /**
     * @param $email
     * @param $password
     * @return mixed
     */
    public function testAccess($email, $password)
    {
        $path   = 'organizations/' . $this->orgId . '/shipments/';
        $result = $this->doRequest($path);
        return ($result);
    }

    /**
     * @param $path
     * @param $data
     * @return mixed
     */
    private function doRequest($path, $data = false)
    {
        $this->initCurl($path, $data);
        $curl_response = curl_exec($this->curl);
        if (curl_errno($this->curl)) {
            $this->log(curl_error($this->curl));
            return false;
        }

        $response = json_decode($curl_response);
        curl_close($this->curl);

        if (isset($response->error)) {
            $this->log($response->error);
            $this->log($path);
            $this->log($data);
//                return false;
        }

        return $response;
    }

    /**
     * @param      $path
     * @param bool $data
     */
    private function initCurl($path, $data = false)
    {
        $service_url = $this->curlUrl . $path;
        //init
        $this->curl = curl_init($service_url);
        //post/get data
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POST, true);
            $post_data = json_encode($data, JSON_FORCE_OBJECT);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($this->curl, CURLOPT_VERBOSE, true);
//            curl_setopt($this->curl, CURLOPT_HEADER, true);
    }

    /**
     * @param $data
     */
    private function log($data)
    {
        $this->_logger->error(print_r($data, true));
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getPointData($name)
    {
        $path   = 'points/' . $name;
        $result = $this->doRequest($path);
        return $result;
    }

    public function getAPIUrl()
    {
        return $this->curlUrl;
    }
}