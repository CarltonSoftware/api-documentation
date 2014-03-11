<?php

require_once 'connect.php';

try {
    
    $res = $client->post(
        '/booking-enquiry',
        array(),
        array(
            'data' => json_encode(
                array(
                    'propertyRef' => 'A182',
                    'brandCode' => 'ZZ',
                    'fromDate' => '2014-05-03',
                    'toDate' => '2014-05-10',
                    'partySize' => 2,
                    'pets' => 0
                )
            )
        )
    )->send();
    
    $data = json_decode($res->getBody(true));
    
    var_dump($data);
    
} catch (\Guzzle\Http\Exception\ClientErrorResponseException $ex) {
    echo $ex->getResponse()->getBody();
} catch (\Guzzle\Http\Exception\ServerErrorResponseException $ex) {
    echo $ex->getResponse()->getBody();
} catch (\Exception $ex) {
    echo $ex->getMessage();
}