<?php

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'HmacPlugin.php';

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
    
$plugin = new \HmacPlugin(APIKEY, APISECRET);

if (!defined('APIURL') || strlen(APIURL) == 0) {
    die('No APIURL constant defined.  You need to set one in config.php.');
}

$client = new Client(APIURL);
$client->addSubscriber($plugin);