<?php

require_once 'connect.php';

try {
    
    $res = $client->get(
        array(
            '/property/{id}',
            array(
                'id' => filter_input(INPUT_GET, 'id')
            )
        )
    )->send();
    
    $data = json_decode($res->getBody(true));
    
    echo sprintf(
        '<p>You\'re looking at: %s!</p>',
        $data->name
    );
    
} catch (\Guzzle\Http\Exception\ClientErrorResponseException $ex) {
    echo $ex->getResponse()->getBody();
} catch (\Guzzle\Http\Exception\ServerErrorResponseException $ex) {
    echo $ex->getResponse()->getBody();
} catch (\Exception $ex) {
    echo $ex->getMessage();
}