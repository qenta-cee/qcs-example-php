<?php
/**
 * QMore Checkout Seamless Demo
 * - Terms of use can be found under
 * https://guides.qenta.com/prerequisites
 * - License can be found under:
 * https://github.com/qenta-cee/qcs-example-php/blob/master/LICENSE.
 */

require_once '../includes/function.inc.php';

// loads the merchant specific parameters from the config file
require_once '../includes/config.inc.php';

session_start();

//--------------------------------------------------------------------------------//
// Computes the fingerprint based on the request parameters used for
// reading of already stored data from the QENTA Data Storage.
//--------------------------------------------------------------------------------//

// initializes the fingerprint seed
// please be aware that the correct order for the fingerprint seed has
// to be the following one:
// customerId, shopId, storageId, secret
$requestFingerprintSeed = '';

// adds the customer id to the fingerprint seed
$requestFingerprintSeed .= $customerId;

// adds the shop id to the fingerprint seed
$requestFingerprintSeed .= $shopId;

// retrieves the storage id from the session already
// initiated within index.php
$storageId = $_SESSION[$STORAGE_ID];

// adds the storageId to the fingerprint seed
$requestFingerprintSeed .= $storageId;

// adds the merchant specific secret to the fingerprint seed
$requestFingerprintSeed .= $secret;

// computes the fingerprint based on SHA512
$requestFingerprint = hash_hmac('sha512', $requestFingerprintSeed, $secret);

//--------------------------------------------------------------------------------//
// Creates and sends a POST request (server-to-server request) to the
// QENTA Checkout Platform for reading data from the QENTA Data Storage.
//--------------------------------------------------------------------------------//

// initiates the string containing all POST parameters and adds them as key-value pairs
$postFields = '';
$postFields .= 'customerId='.$customerId;
$postFields .= '&shopId='.$shopId;
$postFields .= '&storageId='.$storageId;
$postFields .= '&requestFingerprint='.$requestFingerprint;

// is cURL installed yet?
if (!function_exists('curl_init')) {
    exit('Sorry the cURL library is required but was not found! Please follow the instructions in the README about how to install it.');
}

// initializes the libcurl of PHP used for sending a POST request
// to the QENTA Checkout Platform as a server-to-server request
$curl = curl_init();

// sets the required options for the POST request via curl
curl_setopt($curl, CURLOPT_URL, $URL_DATASTORAGE_READ);
curl_setopt($curl, CURLOPT_PORT, $QENTA_CHECKOUT_PORT);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

// sends a POST request to the QENTA Checkout Platform and stores the
// result returned from the QENTA Data Storage in a string for later use
$curlResult = curl_exec($curl);
if (!$curlResult) {
    var_dump($URL_DATASTORAGE_READ);
    $error = curl_error($curl);
    var_dump($error);
}

// closes the connection to the QENTA Checkout Platform
curl_close($curl);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>QMore Checkout Seamless Example</title>
    <link rel="stylesheet" type="text/css" href="style/styles.css">
    <link rel="stylesheet" type="text/css" href="style/q.css">
    <link rel="stylesheet" href="https://use.typekit.net/ucf2gvc.css">
</head>
<body>
<div id="contentDS">
<h1>QMore Checkout Seamless Example</h1>

<h2>Reading sensitive payment specific data from the QENTA Data Storage</h2>
<div id="main">
<p>
    The QENTA Data Storage read operation has been initialized with the following values:
</p>

<table class="payload" border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
    <tr>
        <td align="right"><b>secret</b></td>
        <td><?php echo $secret; ?></td>
    </tr>
    <tr>
        <td align="right"><b>customerId</b></td>
        <td><?php echo $customerId; ?></td>
    </tr>
    <tr>
        <td align="right"><b>shopId</b></td>
        <td><?php echo $shopId; ?></td>
    </tr>
    <tr>
        <td align="right"><b>storageId</b></td>
        <td><?php echo $storageId; ?></td>
    </tr>
    <!-- <tr><td align="right"><b>requestFingerprint</b></td><td><?php echo $requestFingerprint; ?></td></tr> -->
</table>

<p>
    The QENTA Data Storage returned the following values from the read operation:
</p>

<table class="payload" border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
    <?php
    foreach (explode('&', $curlResult) as $keyvalue) {
        $param = explode('=', $keyvalue);
        if (2 == sizeof($param)) {
            $key = urldecode($param[0]);
            $value = urldecode($param[1]);
            echo "<tr><td align='right'><b>".$key.'</b></td><td>'.$value."</td></tr>\n";
        }
    }
    ?>
</table>
</div>
</div>
</body>
</html>
