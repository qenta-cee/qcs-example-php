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
// Computes the protocol, servername, port and path for the various return URLs.
//--------------------------------------------------------------------------------//

$server_URL = getBaseUrl().'frontend/';

//--------------------------------------------------------------------------------//
// Sets the values of all required and optional parameters.
//--------------------------------------------------------------------------------//

$requestFingerprintOrder = '';
$requestFingerprint = '';

// sets values for parameters
$language = 'en';
$amount = $_SESSION['amount'];
$currency = $_SESSION['currency'];
$paymentType = isset($_POST['paymentType']) ? $_POST['paymentType'] : '';
$financialInstitution = isset($_POST['financialInstitution'.$paymentType]) ? $_POST['financialInstitution'.$paymentType] : '';
$orderDescription = 'Jane Doe (33562), Order: 5343643-034';
$successURL = $server_URL.'return_success.html';
$cancelURL = $server_URL.'return_cancel.html';
$failureURL = $server_URL.'return_failure.html';
$serviceURL = $server_URL.'service.html';
$pendingURL = $server_URL.'return_pending.html';
$confirmURL = $server_URL.'confirm.php';
$consumerUserAgent = $_SERVER['HTTP_USER_AGENT'];
$consumerIpAddress = $_SERVER['REMOTE_ADDR'];
$storageId = $_SESSION[$STORAGE_ID];
$orderIdent = $_SESSION['orderIdent'];

// sets consumer data
$consumerBillingFirstname = 'John';
$consumerBillingLastname = 'Doe';
$consumerBillingAddress1 = 'Beispielstraße 10';
$consumerBillingCity = 'Graz';
$consumerBillingZipCode = '8020';
$consumerBillingCountry = 'AT';
$consumerBillingPhone = '0043316898989';
$consumerEmail = 'john.doe@example.com';
$consumerBirthDate = '1990-01-01';

// sets basket data
$basketItems = 1;
$basketItem1ArticleNumber = 42;
$basketItem1Quantity = 1;
$basketItem1Name = 'BasketItem1';
$basketItem1Description = 'TestBasketItem1';
$basketItem1ImageUrl = 'https://www.example.com/picture.png';
$basketItem1UnitGrossAmount = $_SESSION['amount'];
$basketItem1UnitNetAmount = $_SESSION['amount'] * 0.8;
$basketItem1UnitTaxAmount = $_SESSION['amount'] * 0.2;
$basketItem1UnitTaxRate = 20;
$windowName = $CHECKOUT_WINDOW_NAME;

//--------------------------------------------------------------------------------//
// Computes the fingerprint and the fingerprint order.
//--------------------------------------------------------------------------------//

$requestFingerprintSeed = '';
$requestFingerprintOrder .= 'secret,';
$requestFingerprintSeed .= $secret;
$requestFingerprintOrder .= 'customerId,';
$requestFingerprintSeed .= $customerId;
$requestFingerprintOrder .= 'shopId,';
$requestFingerprintSeed .= $shopId;
$requestFingerprintOrder .= 'language,';
$requestFingerprintSeed .= $language;
$requestFingerprintOrder .= 'amount,';
$requestFingerprintSeed .= $amount;
$requestFingerprintOrder .= 'currency,';
$requestFingerprintSeed .= $currency;
$requestFingerprintOrder .= 'financialInstitution,';
$requestFingerprintSeed .= $financialInstitution;
$requestFingerprintOrder .= 'orderDescription,';
$requestFingerprintSeed .= $orderDescription;
$requestFingerprintOrder .= 'successUrl,';
$requestFingerprintSeed .= $successURL;
$requestFingerprintOrder .= 'pendingUrl,';
$requestFingerprintSeed .= $pendingURL;
$requestFingerprintOrder .= 'confirmUrl,';
$requestFingerprintSeed .= $confirmURL;
$requestFingerprintOrder .= 'consumerUserAgent,';
$requestFingerprintSeed .= $consumerUserAgent;
$requestFingerprintOrder .= 'consumerIpAddress,';
$requestFingerprintSeed .= $consumerIpAddress;
$requestFingerprintOrder .= 'storageId,';
$requestFingerprintSeed .= $storageId;
$requestFingerprintOrder .= 'orderIdent,';
$requestFingerprintSeed .= $orderIdent;
$requestFingerprintOrder .= 'consumerBillingFirstname,';
$requestFingerprintSeed .= $consumerBillingFirstname;
$requestFingerprintOrder .= 'consumerBillingLastname,';
$requestFingerprintSeed .= $consumerBillingLastname;
$requestFingerprintOrder .= 'consumerBillingAddress1,';
$requestFingerprintSeed .= $consumerBillingAddress1;
$requestFingerprintOrder .= 'consumerBillingCity,';
$requestFingerprintSeed .= $consumerBillingCity;
$requestFingerprintOrder .= 'consumerBillingZipCode,';
$requestFingerprintSeed .= $consumerBillingZipCode;
$requestFingerprintOrder .= 'consumerBillingCountry,';
$requestFingerprintSeed .= $consumerBillingCountry;
$requestFingerprintOrder .= 'consumerBillingPhone,';
$requestFingerprintSeed .= $consumerBillingPhone;
$requestFingerprintOrder .= 'consumerEmail,';
$requestFingerprintSeed .= $consumerEmail;
$requestFingerprintOrder .= 'consumerBirthDate,';
$requestFingerprintSeed .= $consumerBirthDate;
$requestFingerprintOrder .= 'basketItems,';
$requestFingerprintSeed .= $basketItems;
$requestFingerprintOrder .= 'basketItem1ArticleNumber,';
$requestFingerprintSeed .= $basketItem1ArticleNumber;
$requestFingerprintOrder .= 'basketItem1Quantity,';
$requestFingerprintSeed .= $basketItem1Quantity;
$requestFingerprintOrder .= 'basketItem1Name,';
$requestFingerprintSeed .= $basketItem1Name;
$requestFingerprintOrder .= 'basketItem1Description,';
$requestFingerprintSeed .= $basketItem1Description;
$requestFingerprintOrder .= 'basketItem1ImageUrl,';
$requestFingerprintSeed .= $basketItem1ImageUrl;
$requestFingerprintOrder .= 'basketItem1UnitGrossAmount,';
$requestFingerprintSeed .= $basketItem1UnitGrossAmount;
$requestFingerprintOrder .= 'basketItem1UnitNetAmount,';
$requestFingerprintSeed .= $basketItem1UnitNetAmount;
$requestFingerprintOrder .= 'basketItem1UnitTaxAmount,';
$requestFingerprintSeed .= $basketItem1UnitTaxAmount;
$requestFingerprintOrder .= 'basketItem1UnitTaxRate,';
$requestFingerprintSeed .= $basketItem1UnitTaxRate;

// adds fingerprint order to fingerprint
$requestFingerprintOrder .= 'requestFingerprintOrder';
$requestFingerprintSeed .= $requestFingerprintOrder;

// computes the request fingerprint
$requestFingerprint = hash_hmac('sha512', $requestFingerprintSeed, $secret);

//--------------------------------------------------------------------------------//
// Creates and sends a POST request (server-to-server request) to the
// QMore Checkout Seamless for initiating the checkout.
//--------------------------------------------------------------------------------//

// initiates the string containing all POST parameters and
// adds them as key-value pairs to the post fields
$postFields = '';

$postFields .= 'customerId='.$customerId;
$postFields .= '&shopId='.$shopId;
$postFields .= '&amount='.$amount;
$postFields .= '&currency='.$currency;
$postFields .= '&paymentType='.$paymentType;
$postFields .= '&financialInstitution='.$financialInstitution;
$postFields .= '&language='.$language;
$postFields .= '&orderDescription='.$orderDescription;
$postFields .= '&successUrl='.$successURL;
$postFields .= '&cancelUrl='.$cancelURL;
$postFields .= '&failureUrl='.$failureURL;
$postFields .= '&serviceUrl='.$serviceURL;
$postFields .= '&pendingUrl='.$pendingURL;
$postFields .= '&confirmUrl='.$confirmURL;
$postFields .= '&requestFingerprintOrder='.$requestFingerprintOrder;
$postFields .= '&requestFingerprint='.$requestFingerprint;
$postFields .= '&consumerUserAgent='.$consumerUserAgent;
$postFields .= '&consumerIpAddress='.$consumerIpAddress;
$postFields .= '&storageId='.$storageId;
$postFields .= '&orderIdent='.$orderIdent;
$postFields .= '&consumerBillingFirstname='.$consumerBillingFirstname;
$postFields .= '&consumerBillingLastname='.$consumerBillingLastname;
$postFields .= '&consumerBillingAddress1='.$consumerBillingAddress1;
$postFields .= '&consumerBillingCity='.$consumerBillingCity;
$postFields .= '&consumerBillingZipCode='.$consumerBillingZipCode;
$postFields .= '&consumerBillingCountry='.$consumerBillingCountry;
$postFields .= '&consumerBillingPhone='.$consumerBillingPhone;
$postFields .= '&consumerEmail='.$consumerEmail;
$postFields .= '&consumerBirthDate='.$consumerBirthDate;
$postFields .= '&basketItems='.$basketItems;
$postFields .= '&basketItem1ArticleNumber='.$basketItem1ArticleNumber;
$postFields .= '&basketItem1Quantity='.$basketItem1Quantity;
$postFields .= '&basketItem1Name='.$basketItem1Name;
$postFields .= '&basketItem1Description='.$basketItem1Description;
$postFields .= '&basketItem1ImageUrl='.$basketItem1ImageUrl;
$postFields .= '&basketItem1UnitGrossAmount='.$basketItem1UnitGrossAmount;
$postFields .= '&basketItem1UnitNetAmount='.$basketItem1UnitNetAmount;
$postFields .= '&basketItem1UnitTaxAmount='.$basketItem1UnitTaxAmount;
$postFields .= '&basketItem1UnitTaxRate='.$basketItem1UnitTaxRate;
$postFields .= '&windowName='.$windowName;

// initializes the libcurl of PHP used for sending a POST request
// to the QENTA Data Storage as a server-to-server request
// (please be aware that you have to use a web server where a
// server-to-server request is enabled)

// sets the required options for the POST request via curl
// sends a POST request to the QENTA Checkout Platform and stores the
// result returned from the QENTA Data Storage in a string for later use

// closes the connection to the QENTA Checkout Platform

$curlResult = IssueRequest($URL_FRONTEND_INIT, $postFields);

//--------------------------------------------------------------------------------//
// Retrieves the value for the redirect URL.
//--------------------------------------------------------------------------------//

$redirectURL = '';
foreach (explode('&', $curlResult) as $keyvalue) {
    $param = explode('=', $keyvalue);
    if (2 == sizeof($param)) {
        $key = urldecode($param[0]);
        if ('redirectUrl' == $key) {
            $redirectURL = urldecode($param[1]);

            break;
        }
    }
}

//--------------------------------------------------------------------------------//
// Redirects consumer to payment page.
//--------------------------------------------------------------------------------//

if ('' == $redirectURL) {
    echo '<pre>';
    echo "Frontend Intitiation failed with errors:\n\n";
    foreach (explode('&', $curlResult) as $keyvalue) {
        $param = explode('=', $keyvalue);
        if (2 == sizeof($param)) {
            $key = urldecode($param[0]);
            $value = urldecode($param[1]);
            echo $key.' = '.$value."\n";
        }
    }
    echo '</pre>';
} else {
    header('Location: '.$redirectURL);
}
