<?php

include 'config.php';

// Load client
require_once 'helpers/autoload.php';

// Create a new API singleton.  Client objects can then be created via the
// static method \tabs\api\client\ApiClient::getApi().
\tabs\api\client\ApiClient::factory(APIURL, APIKEY, APISECRET);