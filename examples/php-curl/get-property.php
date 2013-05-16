<?php

// Simple curl class
require_once 'helpers/ApiClient.class.php';

// The URI to request
$uri = 'http://carltonsoftware.apiary.io/';

// New curl request
$client = new ApiClient($uri);

// Get a new property object
$curlResponse = $client->get('/property/mousecott_SS');

$property = $curlResponse->response;

?>

<h1><?php echo $property->name ?></h1>
<ul>
    <li><label>Sleeps:</label> <?php echo $property->accommodates; ?></li>
    <li><label>Bedrooms:</label> <?php echo $property->bedrooms; ?></li>
    <li><label>Pets:</label> <?php echo ($property->pets) ? 'Yes' : 'No'; ?></li>
    <li><label>Price:</label> &pound;<?php echo $property->brands->SS->pricing->ranges->{date('Y')}->low; ?> to &pound;<?php echo $property->brands->SS->pricing->ranges->{date('Y')}->high; ?></li>
</ul>

<?php echo $property->brands->SS->description; ?>
