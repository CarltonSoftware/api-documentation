<?php

/**
 * Tabs Rest API Exception object.
 *
 * PHP Version 5.3
 * 
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */
 
namespace tabs\api\client;

/**
 * Tabs Rest API Exception object.
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class ApiException extends \RuntimeException
{
    /**
     * Exception message 
     * 
     * @var string
     */
    protected $apiExceptionMessage = '';
    
    /**
     * Exception code 
     * 
     * @var integer
     */
    protected $apiExceptionCode = 0;


    // ------------------ Public Functions --------------------- //
    
    /**
     * Constructor
     * 
     * @param object     $response Api Response
     * @param string     $message  Exception message
     * @param integer    $code     Optional Exception code
     * @param \Exception $previous Optional previous exception
     */
    public function __construct(
        $response,
        $message, 
        $code = 0, 
        \Exception $previous = null
    ) {
        // Set overide params
        $this->setMessageFromResponse($response, $message);
        $this->setCodeFromResponse($response, $code);
        
        parent::__construct(
            $this->apiExceptionMessage, 
            $this->apiExceptionCode, 
            $previous
        );
    }

    /**
     * Custom string representation of object
     * 
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s: [%s]: %s',
            __CLASS__,
            $this->code,
            $this->message
        );
    }
    
    /**
     * Set the message of exception to be the response from API
     * 
     * @param object $response Object from the json response
     * @param string $message  Default client error message
     * 
     * @return void
     */
    public function setMessageFromResponse($response, $message)
    {
        $newMsg = $this->_getErrorResponseFromObject($response, "errorDescription");
        if ($newMsg) {
            $this->apiExceptionMessage = $newMsg;
        } else {
            // Set default message which can be provided by the client
            $this->apiExceptionMessage = $message;
        }
    }
    
    /**
     * Set the code of exception to be the response from API
     * 
     * @param object  $response Object from the json response
     * @param integer $code     Default client error code
     * 
     * @return void
     */
    public function setCodeFromResponse($response, $code)
    {
        $newCode = $this->_getErrorResponseFromObject($response, "errorCode");
        if ($newCode) {
            $this->apiExceptionCode = $newCode;
        } else {
            // Set default code which can be provided by the client
            $this->apiExceptionCode = $code;
        }
    }
    
    /**
     * Return the api exception code
     * 
     * @return integer
     */
    public function getApiCode()
    {
        return $this->apiExceptionCode;
    }
    
    /**
     * Return the api exception message
     * 
     * @return string
     */
    public function getApiMessage()
    {
        return $this->apiExceptionMessage;
    }
    
    // ------------------ Private Functions --------------------- //
    
    /**
     * Checks the API response from the object
     * 
     * @param object $response Object from the json response
     * @param string $key      Object key to return
     * 
     * @return mixed 
     */
    private function _getErrorResponseFromObject($response, $key)
    {
        if ($response 
            && property_exists($response, "response")
            && property_exists($response->response, $key)
        ) {
            return $response->response->$key;
        }
        return false;
    }
}
