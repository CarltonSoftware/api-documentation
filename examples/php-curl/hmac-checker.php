<?php

require 'helpers/HMAC.php';

$requiredParams = array('APIKEY', 'APISECRET', 'data');
$generateHash = true;
$params = array();
foreach ($requiredParams as $param) {
    if (in_array($param, array('APIKEY', 'APISECRET'))) {
        continue;
    }
    if (!isset($_POST[$param])) {
        //die (sprintf('%s is required', $param));
        $generateHash = false;
    } else {
        $params[$param] = $_POST[$param];
    }
}

if ($params['data'] == '') {
    unset ($params['data']);
}



if ($generateHash) {
    $hmacHelper = new HMAC();
    $encodedParams = $hmacHelper->hmacEncode($params, $_POST['APISECRET'], $_POST['APIKEY']);
    printf('<div class="info">Hash: %s</div>', $encodedParams['hash']);
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
                <input type="text" name="APIKEY" value="<?php print (isset($_POST['APIKEY']) ? $_POST['APIKEY'] : '');?>">
            </div>

            <div>
                <label for="APISECRET">API Secret</label>
                <input type="text" name="APISECRET" value="<?php print (isset($_POST['APISECRET']) ? $_POST['APISECRET'] : '');?>">
            </div>

            <div>
                <label for="data">Data (or blank if performing a GET)</label>
                <textarea name="data" class="input-data"><?php print (isset($_POST['data']) ? $_POST['data'] : '');?></textarea>
            </div>

            <input type="submit" value="Calculate Hash">
        </form>
    </body>
</html>