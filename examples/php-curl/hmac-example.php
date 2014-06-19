<?php

// Load client
require_once 'helpers/autoload.php';

// API Key
$apiKey = 'a7c22b78dafe908ec32';

// API Secret
$apiSecret = '3f2cb90978aced23f';

// The parameters to encode
$params = array(
    'cow' => 'moo',
    'dog' => 'woof',
    'duck' => 'quack'
);

// Encode the params
$encodedParams = \tabs\api\client\Hmac::hmacEncode($params, $apiKey, $apiSecret);

// Print the results
printf('<h1>HMAC Example</h1>');
printf('<h2>API Key Details</h2>Key: %s<br />Secret: %s', $apiKey, $apiSecret);
printf('<h2>Input Params</h2><pre>%s</pre>', var_export($params, true));
printf('<h2>Output Params</h2><pre>%s</pre>', var_export($encodedParams, true));
printf('<h2>Request URL</h2>http://api.example.com?%s', http_build_query($encodedParams));
