<?php

/**
 * Tabs Rest API object.
 *
 * PHP Version 5.3
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Carlton Software <support@carltonsoftware.co.uk>
 * @copyright 2012 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.carltonsoftware.co.uk
 */

/**
 * Helper class for HMAC authentication.
 */
require_once __DIR__
    . DIRECTORY_SEPARATOR
    . '..'
    . DIRECTORY_SEPARATOR
    . 'helpers'
    . DIRECTORY_SEPARATOR
    . 'HMAC.php';

/**
 * Tabs Rest API client
 *
 * Method calls start from the public accessor methods into:
 *  $this->get|post|put|delete
 *      -> $this->_doRequest()
 *          -> $this->_get|_post|_delete|_put
 *          -> $this->_curlExec()
 *          -> $this->_getResponseData()
 *          -> $this->_outputResponse()
 *
 * @category  API_Client
 * @package   Tabs
 * @author    Carlton Software <support@carltonsoftware.co.uk>
 * @copyright 2012 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class ApiClient
{

    /**
     * Client content type
     *
     * @var string
     */
    protected $contentType = "application/json";

    /**
     * Api version
     *
     * @var string
     */
    protected $apiVersion = "";

    /**
     * Local route string
     *
     * @var string
     */
    protected $urlRoute = "";

    /**
     * Test mode bool.  If set to true, client pops the data param from
     * the provided params array
     *
     * @var boolean
     */
    protected $testMode;

    /**
     * Static route object
     *
     * @var resource
     */
    protected $resource;

    /**
     * The API key to use for HMAC authentication/
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The API secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Array of routes requested by this api instance
     *
     * @var array
     */
    public $routes = array();

    /**
     * An HMAC object which contains functions to do HMAC authentication.
     *
     * @var HMAC
     */
    public $hmacHelper;

    /**
     * Initialise
     *
     * @param string $urlRoute   Route of the API
     * @param string $apiVersion Version Number
     * @param string $apiKey     API HMAC Key
     * @param string $secret     API Secret key
     *
     * @throws Exception
     */
    public function __construct(
        $urlRoute,
        $apiVersion = '',
        $apiKey = '',
        $secret = ''
    ) {
        // Look for the curl module
        if (!function_exists("curl_exec")) {
            throw new Exception("Could not find curl_exec, is CURL installed?");
        }

        // Setup required fields
        $this->setUrlRoute($urlRoute);
        $this->setApiVersion($apiVersion);
        $this->setApiKey($apiKey);
        $this->setSecret($secret);
        $this->hmacHelper = new HMAC();
    }


    // ------------------ Public Functions ----------------------//

    /**
     * Sets the api version
     *
     * @param string $versionNumber Version Number
     *
     * @return void
     */
    public function setApiVersion($versionNumber)
    {
        $this->apiVersion = $versionNumber;
    }

    /**
     * Sets the api url
     *
     * @param string $urlRoute Route of the API
     *
     * @return void
     */
    public function setUrlRoute($urlRoute)
    {
        $this->urlRoute = trim($urlRoute, "/");
    }

    /**
     * Sets the API key.
     *
     * @param string $apiKey The API key to use.
     *
     * @return void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Sets the API secret.
     *
     * @param string $secret The API secret.
     *
     * @return void
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Gets the API secret.
     *
     * @return string The API secret.
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Gets the API key.
     *
     * @return string The API key that is set for this client.
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Sets the api test mode
     *
     * @param boolean $bool true to set test mode
     *
     * @return void
     */
    public function setTestMode($bool)
    {
        $this->testMode = $bool;
    }

    /**
     * Returns the url of the api
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->urlRoute;
    }

    /**
     * Get API call
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return mixed
     */
    public function get($urlPath, $params = array())
    {
        return $this->_doRequest("_get", $urlPath, $params);
    }

    /**
     * Post API call
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return mixed
     */
    public function post($urlPath, $params = array())
    {
        return $this->_doRequest("_post", $urlPath, $params);
    }

    /**
     * Put API call
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return mixed
     */
    public function put($urlPath, $params = array())
    {
        return $this->_doRequest("_put", $urlPath, $params);
    }

    /**
     * Delete API call
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return mixed
     */
    public function delete($urlPath, $params = array())
    {
        return $this->_doRequest("_delete", $urlPath, $params);
    }

    /**
     * Options API call
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return mixed
     */
    public function options($urlPath, $params = array())
    {
        return $this->_doRequest("_options", $urlPath, $params);
    }


    // ------------------ Private Functions ---------------------//

    /**
     * Function used to call the api functions, _get, _post, _put & _delete
     *
     * @param string $apiFunc The request type (get/put/delete/post/head)
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return boolean or result
     */
    private function _doRequest($apiFunc, $urlPath, $params)
    {
        if (method_exists($this, $apiFunc)) {

            $params = $this->hmacHelper->hmacEncode(
                $params,
                $this->getSecret(),
                $this->getApiKey()
            );

            // Request curl response
            $this->$apiFunc($urlPath, $params);

            // Grab response
            $response = $this->_curlExec();

            // Return $header, $body, $location etc from new request
            extract($this->_getResponseData($response));

            // Get headers and put them into a key => value array
            $headers = array();
            $headerLines = array_filter(explode("\r\n", $header));
            foreach ($headerLines as $line) {
                $parts = explode(':', $line, 2);
                if (sizeof($parts) == 2) {
                    $headers[trim($parts[0])] = trim($parts[1]);
                }
            }

            // Attempt to output response.
            // All variables apart from $headers are returned from the
            // extract call on the _getResponseData function
            return $this->_outputResponse(
                $headers,
                $body,
                $info,
                $statusCode,
                $location
            );
        } else {
            return false;
        }
    }

    /**
     * Completes the curl request, also adds the content type header
     *
     * @param boolean $follow Set to true if the client should follow its
     *                        location header
     *
     * @return string
     */
    private function _curlExec($follow = false)
    {
        // Include headers in response string
        curl_setopt($this->resource, CURLOPT_HEADER, 1);
        curl_setopt($this->resource, CURLOPT_VERBOSE, 0);

        // return the output into a variable
        curl_setopt($this->resource, CURLOPT_RETURNTRANSFER, 1);

        // Set curl resource to follow redirects
        curl_setopt($this->resource, CURLOPT_FOLLOWLOCATION, $follow);
        
        // Set the User-Agent header
        curl_setopt(
            $this->resource, 
            CURLOPT_USERAGENT, 
            'TABS ApiClient (http://github.com/CarltonSoftware/tocc-api-client)'
        );

        // Commit the curl request and return the response
        $response = curl_exec($this->resource);
        
        return $response;
    }

    /**
     * Return a array of data about the previous response
     *
     * @param string $response HTTP Response body
     *
     * @return array
     */
    private function _getResponseData($response)
    {
        // HTTP status code
        $statusCode = curl_getinfo($this->resource, CURLINFO_HTTP_CODE);

        // Get the header size
        $headerSize = curl_getinfo($this->resource, CURLINFO_HEADER_SIZE);

        // Headers String
        $header = substr($response, 0, $headerSize);

        // Response Body
        $body = substr($response, $headerSize);

        // Response info
        $info = curl_getinfo($this->resource);

        // Get Location if redirect required
        preg_match('/Location:(.*?)\n/i', $header, $matches);
        $location = trim(array_pop($matches));

        return array(
            "statusCode" => $statusCode,
            "headerSize" => $headerSize,
            "header"     => $header,
            "body"       => $body,
            "info"       => $info,
            "location"   => $location
        );
    }


    /**
     * Function use to return the response in the correct format given the
     * content type
     *
     * @param array  $headers    HTTP Headers as a string block
     * @param string $body       HTTP response body
     * @param string $info       Data from curl_getinfo()
     * @param string $statusCode HTTP Status code
     * @param string $location   Location redirect if specified
     *
     * @return mixed
     */
    private function _outputResponse($headers, $body, $info, $statusCode, $location)
    {
        // Continue to output
        $originalBody = $body;
        if ($this->contentType == "text/xml") {
            $body = @simplexml_load_string($body);
        } else if ($this->contentType == "text/plain") {
            // Plain text
        } else {
            $body = json_decode($body);
        }

        // Set null value to empty string
        if ($body === null) {
            $body = "";
        }

        // Close current request
        curl_close($this->resource);

        return (object) array(
            "status" => $statusCode,
            "response" => $body,
            "info" => $info,
            "headers" => $headers,
            "location" => $location,
            "body" => $originalBody
        );
    }


    /**
     * Handles a post request to the api
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return string
     */
    private function _get($urlPath, $params)
    {
        // Add get params if neccessary
        if (count($params) > 0) {
            $urlPath .= "?" . http_build_query($params);
        }

        // Find the curl path to use
        $this->_getResource($urlPath);
    }

    /**
     * Handles a post request to the api
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return string
     */
    private function _post($urlPath, $params)
    {
        // Find the curl path to use
        $this->_getResource($urlPath);

        // use POST
        curl_setopt($this->resource, CURLOPT_POST, 1);

        // Pop data param into pure json as mock server cannot handle parameter
        // arguments
        if ($this->testMode) {
            if (isset($params['data'])) {
                $params = $params['data'];
            }
        }

        // Set the post fields.  Requires the function
        // http_build_query to encode the parameters
        curl_setopt(
            $this->resource,
            CURLOPT_POSTFIELDS,
            is_array($params) ? http_build_query($params) : $params
        );

        //Set the header
        curl_setopt(
            $this->resource,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/x-www-form-urlencoded'
            )
        );

        // Set the content type
        $this->contentType = "application/x-www-form-urlencoded;charset=UTF-8";
    }

    /**
     * Handles a put request to the api
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return string
     */
    private function _put($urlPath, $params)
    {
        // Find the curl path to use
        $this->_getResource($urlPath);

        // Set custom header
        curl_setopt($this->resource, CURLOPT_CUSTOMREQUEST, "PUT");

        // Pop data param into pure json as mock server cannot handle parameter
        // arguments
        if ($this->testMode) {
            if (isset($params['data'])) {
                $params = $params['data'];
            }
        }

        // Set the post fields.  Requires the function
        // http_build_query to encode the parameters
        curl_setopt(
            $this->resource,
            CURLOPT_POSTFIELDS,
            is_array($params) ? http_build_query($params) : $params
        );

        //Set the header
        curl_setopt(
            $this->resource,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/x-www-form-urlencoded'
            )
        );

        // Set the content type
        $this->contentType = "application/x-www-form-urlencoded;charset=UTF-8";
    }

    /**
     * Handles a delete request to the api
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return string
     */
    private function _delete($urlPath, $params)
    {
        // Add get params if neccessary
        if (count($params) > 0) {
            $urlPath .= "?" . http_build_query($params);
        }

        // Find the curl path to use
        $this->_getResource($urlPath);

        // Set custom header
        curl_setopt($this->resource, CURLOPT_CUSTOMREQUEST, "DELETE");
    }

    /**
     * Handles a delete request to the api
     *
     * @param string $urlPath The path being requested
     * @param array  $params  An array of parameters to be sent
     *
     * @return string
     */
    private function _options($urlPath, $params)
    {
        // Add get params if neccessary
        if (count($params) > 0) {
            $urlPath .= "?" . http_build_query($params);
        }
        
        // Find the curl path to use
        $this->_getResource($urlPath);

        // Set custom header
        curl_setopt($this->resource, CURLOPT_CUSTOMREQUEST, "OPTIONS");
    }

    /**
     * Function used to setup the static curl resource
     *
     * @param string $urlPath The path being requested
     *
     * @return void
     */
    private function _getResource($urlPath)
    {
        // Check to see if the absolute path of the url has bee specified
        if (!stristr($urlPath, $this->urlRoute)) {
            // Remove leading and trailing slashes
            if ($this->apiVersion != "") {
                $urlPath = $this->urlRoute . "/"
                        . $this->apiVersion . "/"
                        . trim($urlPath, "/");
            } else {
                $urlPath = $this->urlRoute . "/" . trim($urlPath, "/");
            }
        }

        // Add to route history
        array_push($this->routes, $urlPath);

        // Get resource
        $this->resource = curl_init($urlPath);
    }
}
