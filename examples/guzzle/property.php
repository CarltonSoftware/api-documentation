<?php

require_once 'connect.php';

try {
    
    if (filter_input(INPUT_GET, 'id')) {
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
    } else {
        throw new \Exception('No property id defined in query string');
    }
    
} catch (\Guzzle\Http\Exception\ClientErrorResponseException $ex) {
    echo $ex->getResponse()->getBody();
} catch (\Guzzle\Http\Exception\ServerErrorResponseException $ex) {
    echo $ex->getResponse()->getBody();
} catch (\Exception $ex) {
    echo $ex->getMessage();
}