<?php

// Connection details
define('APIURL', 'http://carltonsoftware.apiary.io');
define('APIKEY', '');
define('APISECRET', '');

// Require curl class
require_once 'helpers/ApiClient.class.php';

// new curl request
$client = new ApiClient(APIURL, '', APIKEY, APISECRET);

try {

	$res = $client->get('/property', array('fields' => 'id', 'pageSize' => '9999'));
	$properties = $res->response;

	var_dump($properties);

} catch(Exception $e) {

}
