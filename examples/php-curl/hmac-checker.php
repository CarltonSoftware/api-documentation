<?php

// Load client
require_once 'helpers/autoload.php';

$generateHash = true;
$params = array(
    'APIKEY' => '',
    'APISECRET' => '',
    'data' => ''
);
foreach ($params as $key => $val) {
    if (filter_input(INPUT_POST, $key) === null 
        || filter_input(INPUT_POST, $key) == ''
    ) {
        $generateHash = false;
    } else {
        $params[$key] = filter_input(INPUT_POST, $key);
    }
}

if ($params['data'] == '' 
    && $params['APIKEY'] != ''
    && $params['APISECRET'] != ''
) {
    unset ($params['data']);
    unset ($params['APIKEY']);
    unset ($params['APISECRET']);
    $generateHash = true;
}

if ($generateHash) {
    $encodedParams = \tabs\api\client\Hmac::hmacEncode(
        $params,
        filter_input(INPUT_POST, 'APISECRET'),
        filter_input(INPUT_POST, 'APIKEY')
    );
    printf(
        '<div class="info">Hash: %s</div>',
        $encodedParams['hash']
    );
}

?>


<html>
    <head>
        <title>HMAC Test</title>
    </head>

    <style>
        body { font-family: Tahoma; }
        form div { margin-bottom: 10px; }
        label { display: block; }
        div.info { color: #fff; background-color: #66c; padding: 10px; margin-bottom: 20px; }
        textarea.input-data { width: 500px; height: 150px; }
    </style>

    <body>
        <h1>HMAC Checker</h1>

        <p>You can use this form to test that your own HMAC implementation matches what we are expecting</p>

        <form action="" method="POST">
            
            <div>
                <label for="APIKEY">API Key</label>
                <input type="text" name="APIKEY" value="<?php print filter_input(INPUT_POST, 'APIKEY') ? filter_input(INPUT_POST, 'APIKEY') : ''; ?>">
            </div>

            <div>
                <label for="APISECRET">API Secret</label>
                <input type="text" name="APISECRET" value="<?php print filter_input(INPUT_POST, 'APISECRET') ? filter_input(INPUT_POST, 'APISECRET') : ''; ?>">
            </div>

            <div>
                <label for="data">Data (or blank if performing a GET)</label>
                <textarea name="data" class="input-data"><?php print filter_input(INPUT_POST, 'data') ? filter_input(INPUT_POST, 'data') : ''; ?></textarea>
            </div>

            <input type="submit" value="Calculate Hash">
        </form>
    </body>
</html>
