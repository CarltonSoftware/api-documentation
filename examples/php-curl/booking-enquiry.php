<?php

include 'create_connection.php';

$client = \tabs\api\client\ApiClient::getApi();

// Request data
$data = array(
    'propertyRef' => '1000',
    'brandCode' => 'NO',
    'fromDate' => '2018-10-27',
    'toDate' => '2018-11-03',
    'partySize' => 1,
    'pets' => 0
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