<?php

/**
 * HMAC Authenitcation class
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
 * HMAC Authenitcation class.  This class provides static methods
 * for authentication with the tabs api given a provided username
 * and key.
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
class Hmac
{
    /**
     * Encode function
     *
     * @param array  $params Parameters to encode
     * @param string $secret Secret key
     * @param string $key    Key
     *
     * @return array
     */
    static function hmacEncode($params, $secret, $key)
    {
        $params['APIKEY'] = $key;
        $params['APISECRET'] = $secret;
        ksort($params);
        $params = array_map('strval', $params);
        $hash = self::hash(json_encode($params));
        $params['hash'] = $hash;
        unset($params['APISECRET']);
        return $params;
    }
    
    /**
     * Check function
     *
     * @param string $params Parameters to check
     * @param string $secret Secret key
     *
     * @return boolean
     */
    static function hmacCheck($params, $secret)
    {
        $hash = $params['hash'];
        unset($params['hash']);
        $params['APISECRET'] = $secret;
        ksort($params);
        $params = array_map('strval', $params);
        $_hash = self::hash(json_encode($params));
        return ($_hash == $hash);
    }
    
    /**
     * Hash function
     *
     * @param array $data Data
     *
     * @return string
     */
    static function hash($data)
    {
        return hash('SHA256', $data, false);
    }
}
