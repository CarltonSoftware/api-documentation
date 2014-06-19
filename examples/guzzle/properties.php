<?php

require_once 'connect.php';

try {
    
    $res = $client->get(
        '/property',
        array(),
        array(
            'query' => getParams()
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

/**
 * Helper function to define the query parameters passed to the property
 * search endpoint.
 * 
 * @return array
 */
function getParams()
{
    $queryParams = array();
    $params = filter_input_array(INPUT_GET);
    if ($params) {
        foreach (array('page', 'pageSize', 'orderBy', 'fields', 'searchId') as $term) {
            if (filter_input(INPUT_GET, $term)) {
                $queryParams[$term] = filter_input(INPUT_GET, $term);
                unset($params[$term]);
            }
        }
        
        $queryParams['filter'] = http_build_query($params, null, ':');
    }
    
    return $queryParams;
}