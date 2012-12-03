<?php

//The URI to request
$uri = 'http://carltonsoftware.apiary.io/property/mousecott_SS';

//Create a new curl connection
$ch = curl_init();

// Set the URI that we want to request
curl_setopt($ch, CURLOPT_URL, $uri);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, '3');

//Perform the request, and assign the response to $response
$response = curl_exec($ch);

//Convert the response into a json_objecy
$property = json_decode($response);

?>

<h1><?= $property->name ?></h1>
<ul>
    <li><label>Sleeps:</label> <?=$property->accommodates?></li>
    <li><label>Bedrooms:</label> <?=$property->bedrooms?></li>
    <li><label>Pets:</label> <?= ($property->pets) ? 'Yes' : 'No' ?></li>
    <li><label>Price:</label> &pound;<?=$property->brands->SS->pricing->ranges->{date('Y')}->low ?> to &pound;<?=$property->brands->SS->pricing->ranges->{date('Y')}->high ?></li>
</ul>

<?= $property->brands->SS->description ?>
