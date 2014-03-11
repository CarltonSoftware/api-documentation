<?php

require_once 'connect.php';

try {
    
    $res = $client->get(
        '/property',
        array(),
        array(
            'query' => $_GET
        )
    )->send();
    
    $data = json_decode($res->getBody(true));
    echo sprintf('<p>%s properties found</p>', $data->totalResults);
    
    if (count($data->results) > 0) {        
        foreach ($data->results as $property) {
            echo sprintf(
                '<p><a href="property.php?id=%s">%s (%s)</a></p>',
                $property->id,
                $property->name,
                $property->propertyRef
            );
        }
    }
    
} catch (\Guzzle\Http\Exception\ClientErrorResponseException $ex) {
    echo $ex->getResponse()->getBody();
} catch (\Guzzle\Http\Exception\ServerErrorResponseException $ex) {
    echo $ex->getResponse()->getBody();
} catch (\Exception $ex) {
    echo $ex->getMessage();
}