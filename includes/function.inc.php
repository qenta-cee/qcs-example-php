<?php

// to avoid having customer modify PHP module configuration
require_once 'curl-polyfill.php';

function getBaseUrl()
{
    $url = [];
    $ssl = isset($_SERVER['HTTPS']) && 'on' === $_SERVER['HTTPS'] ? true : false;
    $host = $_SERVER['SERVER_NAME'];
    $port = intval($_SERVER['SERVER_PORT']);
    array_push(
        $url,
        'http'.($ssl ? 's' : '').'://',
        $host,
        (($ssl && 443 !== $port) || (!$ssl && 80 !== $port)) ? ':'.$port : '',
        '/'
    );

    return join('', $url);
}

?>
