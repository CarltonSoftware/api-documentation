<?php

include 'create_connection.php';

$client = \tabs\api\client\ApiClient::getApi();

// Get a new property object
$curlResponse = $client->get('/property/mousecott_SS');

$property = $curlResponse->response;

?>

<h1><?php echo $property->name ?></h1>
<ul>
    <li><label>Sleeps:</label> <?php echo $property->accommodates; ?></li>
    <li><label>Bedrooms:</label> <?php echo $property->bedrooms; ?></li>
    <li><label>Pets:</label> <?php echo ($property->pets) ? 'Yes' : 'No'; ?></li>
</ul>