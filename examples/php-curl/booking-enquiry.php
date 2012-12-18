<?php

// Simple curl class
require_once 'helpers/SimpleCURL.class.php';

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

$curl = new SimpleCURL();
$curlResponse = $curl->post($uri, array('data' => $data));

//Convert the response into a json_objecy
$bookingEnquiry = $curlResponse->response;

if (isset($bookingEnquiry->errorCode)) {
    die(
        sprintf(
            '<h2>An error occurred</h2> <strong>Message:</strong> %s<br /> <strong>Code:</strong> %s', 
            $bookingEnquiry->errorDescription, 
            $bookingEnquiry->errorCode
        )
    );
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
