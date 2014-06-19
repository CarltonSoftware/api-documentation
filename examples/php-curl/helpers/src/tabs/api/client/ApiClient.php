<?php

/**
 * Tabs Rest API object.
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
 * @author    Alex Wyett <alex@wyett.co.uk>
 * @copyright 2013 Carlton Software
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   Release: 1
 * @link      http://www.carltonsoftware.co.uk
 */
class ApiClient
{
    /**
     * Static api instance
     *
     * @var ApiClient
     */
    static $api;

    /**
     * Client content type
     *
     * @var string
     */
    protected $contentType = "application/json";

    /**
     * Local route string
     *
     * @var string
     */
    protected $urlRoute = '';

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
     * @var tabs\api\client\HMAC
     */
    public $hmacHelper;

    /**
     * Create a new Api Connection for use within the tabs php client
     * api.
     *
     * @param string $apiUrl Url of the api
     * @param string $apiKey API Key
     * @param string $secret HMAC Secret Key
     *
     * @return \tabs\api\client\ApiClient
     */
    public static function factory(
        $apiUrl,
        $apiKey = '',
        $secret = ''
    ) {
        self::$api = new ApiClient($apiUrl, $apiKey, $secret);
        return self::$api;
    }

    /**
     * Get the api connection
     *
     * @return ApiClient
     */
    public static function getApi()
    {
        // Check for an existing api object
        if (!self::$api) {
            throw new ApiException(null, 'No api connection available');
        }

        return self::$api;
    }

    /**
     * Initialise
     *
     * @param string $urlRoute Route of the API
     * @param string $apiKey   API HMAC Key
     * @param string $secret   API Secret key
     *
     * @throws Exception
     */
    public function __construct(
        $urlRoute,
        $apiKey = '',
        $secret = ''
    ) {
        // Look for the curl module
        if (!function_exists("curl_exec")) {
            throw new \Exception("Could not find curl_exec, is CURL installed?");
        }

        // Setup required fields
        $this->setUrlRoute($urlRoute);
        $this->setApiKey($apiKey);
        $this->setSecret($secret);
        $this->hmacHelper = new \tabs\api\client\Hmac();
    }


    // ------------------ Public Functions ----------------------//

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
     * Returns all of the urls that have been accessed in the api instance
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Multiple get requests
     *
     * @param array $paths Array of url paths and parameters to be requested in
     *                     in path/param key value pairs
     *
     * @return array
     */
    public function mGet($paths)
    {
        return $this->_doMultiRequest('_get', $paths);
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

    /**
     * Return the url parameters with the included hmac hash
     *
     * @param array $params Parameters to encode
     *
     * @return array
     */
    public function getHmacParams($params = array())
    {
        if ($this->getApiKey() != '' && $this->getSecret() != '') {
            return $this->hmacHelper->hmacEncode(
                $params,
                $this->getSecret(),
                $this->getApiKey()
            );
        } else {
            return $params;
        }
    }

    /**
     * Return the url parameters with the included hmac hash in query string
     * format.
     *
     * @param array $params Parameters to encode
     *
     * @return array
     */
    public function getHmacQuery($params = array())
    {
        return $this->_getHttpQuery(
            $this->getHmacParams($params)
        );
    }


    // ------------------ Private Functions ---------------------//


    /**
     * Function used to call the api functions, _get, _post, _put & _delete
     * multiple times!
     *
     * @param string $apiFunc The request type (get/put/delete/post/head)
     * @param array  $paths   Array of paths to be executed
     *
     * @return boolean or result
     */
    private function _doMultiRequest($apiFunc, $paths)
    {
        if (count($paths) > 20) {
            throw new \tabs\api\client\ApiException(
                null,
                'Number of multi connections must not exceed 20'
            );
        }

        $mResource = curl_multi_init();
        $resources = array();
        $results = array();
        foreach ($paths as $path) {
            $this->$apiFunc(
                $path['path'],
                $this->getHmacParams($path['params'])
            );
            $this->_setCurlOpt();
            $resources[] = $this->resource;
            curl_multi_add_handle($mResource, $this->resource);
            curl_close($this->resource);
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mResource, $running);
        } while ($running > 0);

        foreach ($resources as $res) {
            extract(
                $this->_getResponseData(
                    curl_multi_getcontent($res),
                    $res
                )
            );
            $results[] = $this->_outputResponse(
                $headers,
                $body,
                $info,
                $statusCode,
                $location
            );
            curl_multi_remove_handle($mResource, $res);
        }

        curl_multi_close($mResource);
        return $results;
    }

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
            // Get parameters with hmac hash
            $params = $this->getHmacParams($params);

            // Request curl response
            $this->$apiFunc($urlPath, $params);

            // Grab response
            $response = $this->_curlExec();

            // Return $header, $body, $location etc from new request
            extract($this->_getResponseData($response));

            // Close current request
            curl_close($this->resource);

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
     * Sets the options for the curl request, also adds the content type header
     *
     * @param boolean $follow Set to true if the client should follow its
     *                        location header
     *
     * @return void
     */
    private function _setCurlOpt($follow = false)
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
        // Set the curl options
        $this->_setCurlOpt($follow);

        // Commit the curl request and return the response
        return curl_exec($this->resource);
    }

    /**
     * Return a array of data about the previous response
     *
     * @param string   $response HTTP Response body
     * @param resource $resource CURL Handle
     *
     * @return array
     */
    private function _getResponseData($response, $resource = null)
    {
        if (!$resource) {
            $resource = $this->resource;
        }
        // Extract HTTP status code and header size
        extract($this->_getHeaderAndStatus($resource));

        // Headers String
        $header = substr($response, 0, $headerSize);

        // Response Body
        $body = substr($response, $headerSize);

        // Get Location if redirect required
        preg_match('/Location:(.*?)\n/i', $header, $matches);
        $location = trim(array_pop($matches));

        // Get headers and put them into a key => value array
        $headers = array();
        $headerLines = array_filter(explode("\r\n", $header));
        foreach ($headerLines as $line) {
            $parts = explode(':', $line, 2);
            if (sizeof($parts) == 2) {
                $headers[trim($parts[0])] = trim($parts[1]);
            }
        }

        return array(
            "statusCode" => $statusCode,
            "headerSize" => $headerSize,
            "header"     => $header,
            "headers"     => $headers,
            "body"       => $body,
            "info"       => $info,
            "location"   => $location
        );
    }

    /**
     * Return the header and status in an array from a specified resource
     *
     * @param resource $resource Curl handle
     *
     * @return array
     */
    private function _getHeaderAndStatus($resource)
    {
        return array(
            'statusCode' => curl_getinfo($resource, CURLINFO_HTTP_CODE),
            'headerSize' => curl_getinfo($resource, CURLINFO_HEADER_SIZE),
            'info'       => curl_getinfo($resource)
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
        $body = json_decode($body);

        // Set null value to empty string
        if ($body === null) {
            $body = '';
        }

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
            $urlPath .= "?" . $this->_getHttpQuery($params);
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
            is_array($params) ? $this->_getHttpQuery($params) : $params
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
            is_array($params) ? $this->_getHttpQuery($params) : $params
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
            $urlPath .= "?" . $this->_getHttpQuery($params);
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
            $urlPath .= "?" . $this->_getHttpQuery($params);
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
            $urlPath = $this->urlRoute . "/" . trim($urlPath, "/");
        }

        // Add to route history
        array_push($this->routes, $urlPath);

        // Get resource
        $this->resource = curl_init($urlPath);
    }
    
    /**
     * Encode an array of parameters into a string
     * 
     * @param array $params Parameters to encode
     * 
     * @return string
     */
    private function _getHttpQuery($params)
    {
        return http_build_query($params, null, '&');
    }
}
