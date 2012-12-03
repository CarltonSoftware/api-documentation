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

//Print out the response
print $response;
