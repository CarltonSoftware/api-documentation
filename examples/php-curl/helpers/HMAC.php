<?php

class HMAC
{
    static function hmacEncode($params, $secret, $key) {
        $params['APIKEY'] = $key;
        $params['APISECRET'] = $secret;
        ksort($params);
        $params = array_map('strval', $params);
        $hash = self::hash(json_encode($params));
        $params['hash'] = $hash;
        unset($params['APISECRET']);

        return $params;
    }

    static function hmacCheck($params, $secret) {
        $hash = $params['hash'];
        unset($params['hash']);
        $params['APISECRET'] = $secret;
        asort($params);
        $_hash = self::hash(json_encode($params));

        return ($_hash == $hash);
    }

    static function hash($data) {
        return hash('SHA256', $data, false);
    }

}