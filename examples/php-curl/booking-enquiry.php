<?php

//The URI to request
$uri = 'http://carltonsoftware.apiary.io/booking-enquiry';

//Request data
$data = json_encode(array(
    'propertyRef' => 'mousecott',
    'brandCode' => 'SS',
    'fromDate' => '2012-07-01',
    'toDate' => '2012-07-08',
    'partySize' => 5,
    'pets' => 2
));

//Create a new curl connection
$ch = curl_init();

// Set the URI that we want to request
curl_setopt($ch, CURLOPT_URL, $uri);
curl_setopt($ch,CURLOPT_POST, 1); //we want to use POST
curl_setopt($ch,CURLOPT_POSTFIELDS, array('data' => $data)); //the data to post
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, '3');

//Perform the request, and assign the response to $response
$response = curl_exec($ch);

//Convert the response into a json_objecy
$bookingEnquiry = json_decode($response);

if (isset($bookingEnquiry->errorCode)) {
    die (sprintf('<h2>An error occurred</h2> <strong>Message:</strong> %s<br /> <strong>Code:</strong> %s', $bookingEnquiry->errorDescription, $bookingEnquiry->errorCode));
}


$extrasPrice = 0;
foreach ($bookingEnquiry->price->extras as $extra) {
    $extrasPrice += $extra->totalPrice;
}
?>

<h1>Your Holiday Price</h1>
<ul>
    <li>
        <label>Basic Price:</label>
        &pound;<?= $bookingEnquiry->price->basicPrice ?>
    </li>
    <li>
        <label>Extras Price:</label>
        &pound;<?= $extrasPrice ?>
    </li>
    <li style="font-weight: bold;">
        <label>Total Price:</label>
        &pound;<?= $bookingEnquiry->price->totalPrice ?>
    </li>
</ul>
