<?php

// Load client
require_once 'helpers/autoload.php';

// The URI to request
$uri = 'http://carltonsoftware.apiary.io/';
$key = '';
$secret = '';

// New curl request
$client = \tabs\api\client\ApiClient::factory($uri, $key, $secret);

// Get a new property object
$curlResponse = $client->get('/property/mousecott_SS');

$property = $curlResponse->response;

?>

<h1><?php echo $property->name ?></h1>
<ul>
    <li><label>Sleeps:</label> <?php echo $property->accommodates; ?></li>
    <li><label>Bedrooms:</label> <?php echo $property->bedrooms; ?></li>
    <li><label>Pets:</label> <?php echo ($property->pets) ? 'Yes' : 'No'; ?></li>
    <li><label>Price:</label> &pound;<?php echo $property->brands->SS->pricing->ranges->{'2013'}->low; ?> to &pound;<?php echo $property->brands->SS->pricing->ranges->{'2013'}->high; ?></li>
</ul>

<?php 
    echo $property->brands->SS->description;
