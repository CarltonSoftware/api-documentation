<?php

// Simple curl class
require_once 'helpers/ApiClient.class.php';

//Request data
$data = array(
    'propertyRef' => '10DMTH',
    'brandCode' => 'CD',
    'fromDate' => '2013-08-24',
    'toDate' => '2013-08-31',
    'partySize' => 1,
    'pets' => 0
);

$secret = 'a40feeeaa74121af';
$key = 'a3k98dv7';

$client = new ApiClient(
    'http://cd.api.carltonsoftware.co.uk',
    '',
    $key,
    $secret
);

$response = $client->post(
    '/booking-enquiry', 
    array(
        'data' => json_encode($data)
    )
);

$bookingEnquiry = $response->response;

$extrasPrice = 0;
foreach ($bookingEnquiry->price->extras as $extra) {
    $extrasPrice += $extra->totalPrice;
}

?>

<h1>Your Holiday Price</h1>
<ul>
    <li>
        <label>Basic Price:</label>
        &pound;<?php echo $bookingEnquiry->price->basicPrice; ?>
    </li>
    <li>
        <label>Extras Price:</label>
        &pound;<?php echo $extrasPrice; ?>
    </li>
    <li style="font-weight: bold;">
        <label>Total Price:</label>
        &pound;<?php echo $bookingEnquiry->price->totalPrice; ?>
    </li>
</ul>
