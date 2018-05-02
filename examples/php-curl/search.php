<?php

// Connection details
include 'create_connection.php';

/**
 * Basic property search request
 */
echo '<h2>Basic property search</h2>';
echo search();
    
/**
 * Pet friendly search
 */
echo '<h2>Pet friendly search</h2>';
echo search(1, 10, array('pets' => 'true'));
    
/**
 * Properties sleeping greater than (or equal to) 4, that are 4* rated
 */
echo '<h2>Properties sleeping greater than (or equal to) 4, that are 4* rated</h2>';
echo search(
    1,
    10,
    array(
        'accommodates' => '>4',
        'rating' => '4'
    )
);

/**
 * Output api calls made
 */
echo sprintf(
    '<h2>API Calls made</h2><pre>%s</pre>',
    implode("\n", \tabs\api\client\ApiClient::getApi()->getRoutes())
);

/**
 * Helper function for performing a search
 * 
 * This is just for demonstration purposes.
 */
function search($page = 1, $pageSize = 10, $params = array())
{
    // String response to be returned
    $propertyListing = '';
    
    // New client object
    $client = \tabs\api\client\ApiClient::getApi();

    try {

        /**
         * Property search request
         */
        $res = $client->get(
            '/property',
            array(
                'page' => $page,
                'pageSize' => $pageSize,
                'filter' => http_build_query(
                    $params,
                    null,
                    ':' // Colon separator for filter variables
                )
            )
        );
        $properties = $res->response;
        if (isset($properties->results)) {
            $propertyListing .= sprintf(
                '<p><strong>%s</strong> Found</p>',
                $properties->totalResults
            );
            foreach ($properties->results as $property) {
                $propertyListing .= sprintf(
                    '<p>%s (%s)</p><ul><li>Sleeps %s</li></ul>',
                    $property->name,
                    $property->propertyRef,
                    $property->accommodates
                );
            }
        }

    } catch(Exception $e) {
        $propertyListing = $e->getMessage();
    }
    
    return $propertyListing;
}
