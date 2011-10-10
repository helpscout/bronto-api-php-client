<?php

class Bronto_Api
{
    const BASE_WSDL     = 'https://api.bronto.com/v4?wsdl';
    const BASE_LOCATION = 'https://api.bronto.com/v4';
    const BASE_URL      = 'http://api.bronto.com/v4';

    /**
     * SoapClient object
     *
     * @var SoapClient
     */
    protected $_soapClient;

    /**
     * API token
     *
     * @var string
     */
    protected $_token;

    /**
     * Cache of class objects
     *
     * @var array
     */
    protected $_classCache = array();

    /**
     * Constructor
     *
     * @param string $token
     * @param array $options
     */
    public function __construct($token = null, $options = null)
    {
        if (!extension_loaded('soap')) {
            require_once 'Bronto/Api/Exception.php';
            throw new Bronto_Api_Exception('SOAP extension is not loaded.');
        }

        if ($token !== null) {
            $this->setToken($token);
        }
    }

    /**
     * Login with API token
     *
     * @return Bronto_Api
     */
    public function login()
    {
        try {
            $client    = $this->getSoapClient();
            $sessionId = $client->login(array('apiToken' => $this->getToken()))->return;
        } catch (SoapFault $e) {
            if (strpos($e->getMessage(), 'Authentication failed for token') !== false) {
                throw new Bronto_Api_Exception("Authentication failed for token: {$this->getToken()}");
            }
        }

        $client->__setSoapHeaders(array(
            new SoapHeader(self::BASE_URL, 'sessionHeader', array('sessionId' => $sessionId))
        ));

        return $this;
    }

    /**
     * Set API token
     *
     * @param string $token
     * @return Bronto_Api
     */
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Activity
     */
    public function getActivityObject()
    {
        return $this->getObject('activity');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Contact
     */
    public function getContactObject()
    {
        return $this->getObject('contact');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Delivery
     */
    public function getDeliveryObject()
    {
        return $this->getObject('delivery');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Deliverygroup
     */
    public function getDeliveryGroupObject()
    {
        return $this->getObject('deliverygroup');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Field
     */
    public function getFieldObject()
    {
        return $this->getObject('field');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Message
     */
    public function getMessageObject()
    {
        return $this->getObject('message');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Messagerule
     */
    public function getMessageRuleObject()
    {
        return $this->getObject('messagerule');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_List
     */
    public function getListObject()
    {
        return $this->getObject('list');
    }

    /**
     * Lazy loads our API objects
     *
     * @param string $object
     * @return Bronto_Api_Abstract
     */
    public function getObject($object)
    {
        $object = ucfirst($object);

        if (!isset($this->_classCache[$object])) {
            $className = "Bronto_Api_{$object}";
            if (class_exists($className)) {
                $this->_classCache[$object] = new $className(array('api' => $this));
            } else {
                throw new Exception("Unable to load class: {$className}");
            }
        }

        return $this->_classCache[$object];
    }

    /**
     * @return SoapClient
     */
    public function getSoapClient()
    {
        if ($this->_soapClient == null) {
            $this->_soapClient = new SoapClient(self::BASE_WSDL, array(
                'trace'    => 1,
                'encoding' => 'UTF-8',
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            ));
            $this->_soapClient->__setLocation(self::BASE_LOCATION);
        }
        return $this->_soapClient;
    }

    /**
     * Retrieve request XML
     *
     * @return string
     */
    public function getLastRequest()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastRequest();
        }

        return '';
    }

    /**
     * Get response XML
     *
     * @return string
     */
    public function getLastResponse()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastResponse();
        }

        return '';
    }

    /**
     * Retrieve request headers
     *
     * @return string
     */
    public function getLastRequestHeaders()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastRequestHeaders();
        }

        return '';
    }

    /**
     * Retrieve response headers (as string)
     *
     * @return string
     */
    public function getLastResponseHeaders()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastResponseHeaders();
        }

        return '';
    }
}