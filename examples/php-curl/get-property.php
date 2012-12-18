<?php

// Simple curl class
require_once 'helpers/SimpleCURL.class.php';

// The URI to request
$uri = 'http://carltonsoftware.apiary.io/property/mousecott_SS';

// New curl request
$curl = new SimpleCURL();

// Get a new property object
$curlResponse = $curl->get($uri);

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
