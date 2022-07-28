<?php
/**
 * QMore Checkout Seamless Demo
 * - Terms of use can be found under
 * https://guides.qenta.com/prerequisites
 * - License can be found under:
 * https://github.com/qenta-cee/qcs-example-php/blob/master/LICENSE.
 */

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

function issueRequest($url, $fieldquery)
{
    $options = array(
        'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => $fieldquery,
        ),
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
      );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}
