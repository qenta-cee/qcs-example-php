<?php
//--------------------------------------------------------------------------------//
//                                                                                //
// Wirecard Checkout Seamless Example                                             //
//                                                                                //
// Copyright (c)                                                                  //
// Wirecard Central Eastern Europe GmbH                                           //
// www.wirecard.at                                                                //
//                                                                                //
// THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY         //
// KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE            //
// IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A                     //
// PARTICULAR PURPOSE.                                                            //
//                                                                                //
//--------------------------------------------------------------------------------//
// THIS EXAMPLE IS FOR DEMONSTRATION PURPOSES ONLY!                               //
//--------------------------------------------------------------------------------//
// Please read the integration documentation before modifying this file.          //
//--------------------------------------------------------------------------------//

// loads the merchant specific parameters from the config file
require_once("config.inc.php");

session_start();

//--------------------------------------------------------------------------------//
// Computes the fingerprint based on the request parameters used for the
// initiation of the Wirecard data storage.
//--------------------------------------------------------------------------------//

// initializes the fingerprint seed
// please be aware that the correct order for the fingerprint seed has
// to be the following one:
// customerId, shopId, orderIdent, returnUrl, language, javascriptScriptVersion, secret
$requestFingerprintSeed = "";

// adds the customer id to the fingerprint seed
$requestFingerprintSeed .= $customerId;

// adds the shop id to the fingerprint seed
$requestFingerprintSeed .= $shopId;

// adds the unique identification for the order (order identity) to the fingerprint seed
// (for demonstration purposes only a random string is generated)
$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-";
$randomString = "";
for ($i = 0; $i < 10; $i++) {
    $randomString .= $characters[rand(0, strlen($characters) - 1)];
}
$_SESSION["orderIdent"] = $randomString;
$orderIdent = $_SESSION["orderIdent"];
$requestFingerprintSeed .= $orderIdent;

$url = $WEBSITE_URL;

// adds the return URL to the fingerprint seed
$returnURL = $url . "frontend/fallback_return.php";
$requestFingerprintSeed .= $returnURL;

// adds the language to the fingerprint seed
$language = "en";
$requestFingerprintSeed .= $language;

// adds the JavaScript version to the fingerprint seed
$javascriptScriptVersion = $PCI3_DSS_SAQ_A_ENABLE ? "pci3" : ''; // version can be an empty string
$requestFingerprintSeed .= $javascriptScriptVersion;

// adds the merchant specific secret to the fingerprint seed
$requestFingerprintSeed .= $secret;

// computes the fingerprint based on SHA512 and the fingerprint seed
$requestFingerprint = hash_hmac("sha512", $requestFingerprintSeed, $secret);

//--------------------------------------------------------------------------------//
// Creates and sends a POST request (server-to-server request) to the
// Wirecard Checkout Platform for initiating the Wirecard data storage.
//--------------------------------------------------------------------------------//

// initiates the string containing all POST parameters and
// adds them as key-value pairs to the post fields
$postFields = "";
$postFields .= "customerId=" . $customerId;
$postFields .= "&shopId=" . $shopId;
$postFields .= "&javascriptScriptVersion=" . $javascriptScriptVersion;
$postFields .= "&orderIdent=" . $orderIdent;
$postFields .= "&returnUrl=" . $returnURL;
$postFields .= "&language=" . $language;
$postFields .= "&requestFingerprint=" . $requestFingerprint;

if ($PCI3_DSS_SAQ_A_ENABLE) {
    if ($PCI3_DSS_SAQ_A_IFRAME_CSS_URL)
        $postFields .= '&iframeCssUrl=' . $PCI3_DSS_SAQ_A_IFRAME_CSS_URL;

    if ($PCI3_DSS_SAQ_A_CCARD_SHOW_CVC !== null)
        $postFields .= '&creditcardShowCvcField=' . ($PCI3_DSS_SAQ_A_CCARD_SHOW_CVC ? 'true' : 'false');

    if ($PCI3_DSS_SAQ_A_CCARD_SHOW_ISSUEDATE !== null)
        $postFields .= '&creditcardShowIssueDateField=' . ($PCI3_DSS_SAQ_A_CCARD_SHOW_ISSUEDATE ? 'true' : 'false');

    if ($PCI3_DSS_SAQ_A_CCARD_SHOW_ISSUENUMBER !== null)
        $postFields .= '&creditcardShowIssueNumberField=' . ($PCI3_DSS_SAQ_A_CCARD_SHOW_ISSUENUMBER ? 'true' : 'false');

    if ($PCI3_DSS_SAQ_A_CCARD_SHOW_CARDHOLDERNAME !== null)
        $postFields .= '&creditcardShowCardholderNameField=' . ($PCI3_DSS_SAQ_A_CCARD_SHOW_CARDHOLDERNAME ? 'true' : 'false');
}

// is cURL installed yet?
if (!function_exists('curl_init')){
	die('Sorry the cURL library is required but was not found! Please follow the instructions in the README about how to install it.');
}

// initializes the libcurl of PHP used for sending a POST request
// to the Wirecard data storage as a server-to-server request
// (please be aware that you have to use a web server where a
// server-to-server request is enabled)
$curl = curl_init();

// sets the required options for the POST request via curl
curl_setopt($curl, CURLOPT_URL, $URL_DATASTORAGE_INIT);
curl_setopt($curl, CURLOPT_PORT, $WIRECARD_CHECKOUT_PORT);
curl_setopt($curl, CURLOPT_PROTOCOLS, $WIRECARD_CHECKOUT_PROTOCOL);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

// sends a POST request to the Wirecard Checkout Platform and stores the
// result returned from the Wirecard data storage in a string for later use
$curlResult = curl_exec($curl);
if (!$curlResult) {
	$error = curl_error($curl);
	var_dump($error);
}

// closes the connection to the Wirecard Checkout Platform
curl_close($curl);

//--------------------------------------------------------------------------------//
// Retrieves the storage id and the JavaScript URL returned from the
// initiation of the Wirecard data storage by the previous POST request.
//--------------------------------------------------------------------------------//

// initiates the storage id and javascript URL
$storageId = "";
$javascriptURL = "";

// extracts each key-value pair returned from the previous POST request
foreach (explode('&', $curlResult) as $keyvalue) {
    // splits the key and the name of each key-value pair
    $param = explode('=', $keyvalue);
	
    if (sizeof($param) == 2) {
        // decodes key and value
        $key = urldecode($param[0]);
        $value = urldecode($param[1]);
        if (strcmp($key, "storageId") == 0) {
            $storageId = $value;
            // saves the storage id in a session variable for later use
            // when reading data from the data storage in file read.php
            $_SESSION[$STORAGE_ID] = $storageId;
        }
        if (strcmp($key, "javascriptUrl") == 0) {
            // saves the JavaScript URL in variable for later use within this file
            $javascriptURL = $value;
        }
    }
}
?>
<html>
<head>
    <title>Wirecard Checkout Seamless Example</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <!-- loads the Wirecard data storage JavaScript objects for transferring and storing sensitive data
    to the Wirecard data storage via bypassing your web shop -->
    <script src="<?php echo $javascriptURL; ?>" type="text/javascript"></script>
    <script type="text/javascript">
        // name of selected payment type for storing data
        var paymentType = "";

        // function for storing sensitive data to the Wirecard data storage
        function storeData(aPaymentType) {
            // sets the selected payment type where sensitive data should be stored
            paymentType = aPaymentType;
            // creates a new JavaScript object containing the Wirecard data storage functionality
            var dataStorage = new WirecardCEE_DataStorage();
            // initializes the JavaScript object containing the payment specific information and data
            var paymentInformation = {};
            if (aPaymentType == "Maestro") {
                if (!document.getElementById('maestro_pan')) {
                    dataStorage.storeMaestroInformation(null, callbackFunction);
                } else {
                    paymentInformation.pan = document.getElementById('maestro_pan').value;
                    paymentInformation.expirationMonth = document.getElementById('maestro_expirationMonth').value;
                    paymentInformation.expirationYear = document.getElementById('maestro_expirationYear').value;
                    paymentInformation.cardholdername = document.getElementById('maestro_cardholdername').value;
                    paymentInformation.cardverifycode = document.getElementById('maestro_cardverifycode').value;
                    paymentInformation.issueMonth = document.getElementById('maestro_issueMonth').value;
                    paymentInformation.issueYear = document.getElementById('maestro_issueYear').value;
                    paymentInformation.issueNumber = document.getElementById('maestro_issueNumber').value;
                    // stores sensitive data to the Wirecard data storage
                    dataStorage.storeMaestroInformation(paymentInformation, callbackFunction);
                }
            }
            if (aPaymentType == "CreditCardMoto") {
                if (!document.getElementById('cc_moto_pan')) {
                    dataStorage.storeCreditCardMotoInformation(null, callbackFunction);
                } else {
                    paymentInformation.pan = document.getElementById('cc_moto_pan').value;
                    paymentInformation.expirationMonth = document.getElementById('cc_moto_expirationMonth').value;
                    paymentInformation.expirationYear = document.getElementById('cc_moto_expirationYear').value;
                    paymentInformation.cardholdername = document.getElementById('cc_moto_cardholdername').value;
                    paymentInformation.cardverifycode = document.getElementById('cc_moto_cardverifycode').value;
                    paymentInformation.issueMonth = document.getElementById('cc_moto_issueMonth').value;
                    paymentInformation.issueYear = document.getElementById('cc_moto_issueYear').value;
                    paymentInformation.issueNumber = document.getElementById('cc_moto_issueNumber').value;
                    // stores sensitive data to the Wirecard data storage
                    dataStorage.storeCreditCardMotoInformation(paymentInformation, callbackFunction);
                }
            }
            if (aPaymentType == "CreditCard") {
                if (!document.getElementById('cc_pan')) {
                    dataStorage.storeCreditCardInformation(null, callbackFunction);
                } else {
                    paymentInformation.pan = document.getElementById('cc_pan').value;
                    paymentInformation.expirationMonth = document.getElementById('cc_expirationMonth').value;
                    paymentInformation.expirationYear = document.getElementById('cc_expirationYear').value;
                    paymentInformation.cardholdername = document.getElementById('cc_cardholdername').value;
                    paymentInformation.cardverifycode = document.getElementById('cc_cardverifycode').value;
                    paymentInformation.issueMonth = document.getElementById('cc_issueMonth').value;
                    paymentInformation.issueYear = document.getElementById('cc_issueYear').value;
                    paymentInformation.issueNumber = document.getElementById('cc_issueNumber').value;
                    // stores sensitive data to the Wirecard data storage
                    dataStorage.storeCreditCardInformation(paymentInformation, callbackFunction);
                }
            }
            if (aPaymentType == "SEPA-DD") {
                paymentInformation.bankBic = document.getElementById('sepa-dd_bankBic').value;
                paymentInformation.bankAccountIban = document.getElementById('sepa-dd_bankAccountIban').value;
                paymentInformation.accountOwner = document.getElementById('sepa-dd_accountOwner').value;
                // stores sensitive data to the Wirecard data storage
                dataStorage.storeSepaDdInformation(paymentInformation, callbackFunction);
            }
            if (aPaymentType == "paybox") {
                paymentInformation.payerPayboxNumber = document.getElementById('payerPayboxNumber').value;
                // stores sensitive data to the Wirecard data storage
                dataStorage.storePayboxInformation(paymentInformation, callbackFunction);
            }
            if (aPaymentType == "giropay") {
                paymentInformation.accountOwner = document.getElementById('giropay_accountOwner').value;
                paymentInformation.bankAccount = document.getElementById('giropay_bankAccount').value;
                paymentInformation.bankNumber = document.getElementById('giropay_bankNumber').value;
                // stores sensitive data to the Wirecard data storage
                dataStorage.storeGiropayInformation(paymentInformation, callbackFunction);
            }
            if (aPaymentType == "voucher") {
                paymentInformation.voucherId = document.getElementById('voucherId').value;
                // stores sensitive data to the Wirecard data storage
                dataStorage.storeVoucherInformation(paymentInformation, callbackFunction);
            }
        }

        // callback function for displaying the results of storing the
        // sensitive data to the Wirecard data storage
        callbackFunction = function (aResponse) {
            // initiates the result string presented to the user
            var s = "Response of Wirecard data storage call:\n\n";
            // checks if response status is without errors
            if (aResponse.getStatus() == 0) {
                // saves all anonymized payment information to a JavaScript object
                var info = aResponse.getAnonymizedPaymentInformation();
                if (paymentType == "CreditCard" || paymentType == "CreditCardMoto" || paymentType == "Maestro") {
                    s += "anonymousPan: " + info.anonymousPan + "\n";
                    s += "maskedPan: " + info.maskedPan + "\n";
                    s += "financialInstitution: " + info.financialInstitution + "\n";
                    s += "brand: " + info.brand + "\n";
                    s += "cardholdername: " + info.cardholdername + "\n";
                    s += "expiry: " + info.expiry + "\n";
                }
                if (paymentType == "SEPA-DD") {
                    s += "bankBic: " + info.bankBic + "\n";
                    s += "bankAccountIban: " + info.bankAccountIban + "\n";
                    s += "accountOwner: " + info.accountOwner + "\n";
                }
                if (paymentType == "paybox") {
                    s += "payerPayboxNumber: " + info.payerPayboxNumber + "\n";
                }
                if (paymentType == "giropay") {
                    s += "accountOwner: " + info.accountOwner + "\n";
                    s += "bankAccount: " + info.bankAccount + "\n";
                    s += "bankNumber: " + info.bankNumber + "\n";
                }
                if (paymentType == "voucher") {
                    s += "voucherId: " + info.voucherId + "\n";
                }
            }
            else {
                // collects all occured errors and adds them to the result string
                var errors = aResponse.getErrors();
                for (e in errors) {
                    s += "Error " + e + ": " + errors[e].message + " (Error Code: " + errors[e].errorCode + ")\n";
                }
            }
            // presents result string to the user
            alert(s);
        }
    </script>
</head>
<body>
<h1>Wirecard Checkout Seamless Example</h1>

<p>
    This is a very simple example of the Wirecard Checkout Seamless for demonstration purposes only.
    It shows you step-by-step the usage of the Wirecard data storage for storing sensitive payment data of your consumer,
    the initiation of the checkout process and the handling of the results of the payment done by your consumer:
</p>

<ul>
    <li>Step 1: Initializing the Wirecard data storage</li>
    <li>Step 2: Storing sensitive payment specific data to the Wirecard data storage</li>
    <li>Step 3: Reading sensitive payment specific data from the Wirecard data storage</li>
</ul>

<p>
    If you have any questions regarding the integration please do not hesitate to contact our
    support team (<a href="mailto:support@wirecard.at">support@wirecard.at</a>).
</p>

<hr/>

<h2>Step 1: Initializing the Wirecard data storage</h2>

<p>
    The Wirecard data storage has been initialized with the following values:
</p>

<table border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
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
        <td align="right"><b>orderIdent</b></td>
        <td><?php echo $orderIdent; ?></td>
    </tr>
    <tr>
        <td align="right"><b>returnUrl</b></td>
        <td><?php echo $returnURL; ?></td>
    </tr>
    <tr>
        <td align="right"><b>language</b></td>
        <td><?php echo $language; ?></td>
    </tr>
    <!-- <tr><td align="right"><b>requestFingerprint</b></td><td><?php echo $requestFingerprint; ?></td></tr> -->
</table>

<p>
    The Wirecard Datastorage returned the following values after initialization:
</p>

<table border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
    <?php
    foreach (explode('&', $curlResult) as $keyvalue) {
        $param = explode('=', $keyvalue);
        if (sizeof($param) == 2) {
            $key = urldecode($param[0]);
            $value = urldecode($param[1]);
            echo "<tr><td align='right'><b>" . $key . "</b></td><td>" . $value . "</td></tr>\n";
        }
    }
    ?>
</table>

<h2>Step 2: Storing sensitive payment specific data to the Wirecard data storage</h2>

<p>
    For testing purposes you can store sensitive data of various payment types in
    the Wirecard data storage and after storing the data you are able to read them again.
    (The storing of this sensitive payment data is done on client-side via JavaScript bypassing your web shop.)
</p>

<table border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
    <tr>
        <td colspan="2" align="center"><b>CreditCard</b></td>
    </tr>
    <?php if (!$PCI3_DSS_SAQ_A_ENABLE) { ?>
        <tr>
            <td align="right"><b>pan</b></td>
            <td><input type="text" value="9500000000000001" id="cc_pan"></td>
        </tr>
        <tr>
            <td align="right"><b>expirationMonth</b></td>
            <td><input type="text" value="12" id="cc_expirationMonth"></td>
        </tr>
        <tr>
            <td align="right"><b>expirationYear</b></td>
            <td><input type="text" value="2031" id="cc_expirationYear"></td>
        </tr>
        <tr>
            <td align="right"><b>cardverifycode</b></td>
            <td><input type="text" value="123" id="cc_cardverifycode"></td>
        </tr>
        <tr>
            <td align="right"><b>cardholdername</b></td>
            <td><input type="text" value="Jane Doe" id="cc_cardholdername"></td>
        </tr>
        <tr>
            <td align="right"><b>issueMonth</b></td>
            <td><input type="text" value="01" id="cc_issueMonth"></td>
        </tr>
        <tr>
            <td align="right"><b>issueYear</b></td>
            <td><input type="text" value="2013" id="cc_issueYear"></td>
        </tr>
        <tr>
            <td align="right"><b>issueNumber</b></td>
            <td><input type="text" value="1234" id="cc_issueNumber"></td>
        </tr>
        <tr>
            <td colspan="2" align="right"><input type="button" value="Store CreditCard data" onClick="storeData('CreditCard');"></td>
        </tr>
    <?php } else { ?>
        <tr>
            <td colspan="2">
                <div id="creditcardDataIframe"></div>
            </td>
        </tr>
        <script type="text/javascript">
            var wd = new WirecardCEE_DataStorage();
            wd.buildIframeCreditCard('creditcardDataIframe', '100%', '200px');
        </script>
        <tr>
            <td colspan="2">
                <i>The HTML containing the input fields for sensitive credit card data is hosted at Wirecard.
                    Based on Javascript the consumer input is immediately send to Wirecard, validated and stored in the data storage,
                    therefore no button for storing this credit card data is required.</i>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="2" align="center"><b>CreditCardMoto</b></td>
    </tr>
    <?php if (!$PCI3_DSS_SAQ_A_ENABLE) { ?>
        <tr>
            <td align="right"><b>pan</b></td>
            <td><input type="text" value="9500000000000001" id="cc_moto_pan"></td>
        </tr>
        <tr>
            <td align="right"><b>expirationMonth</b></td>
            <td><input type="text" value="12" id="cc_moto_expirationMonth"></td>
        </tr>
        <tr>
            <td align="right"><b>expirationYear</b></td>
            <td><input type="text" value="2031" id="cc_moto_expirationYear"></td>
        </tr>
        <tr>
            <td align="right"><b>cardverifycode</b></td>
            <td><input type="text" value="123" id="cc_moto_cardverifycode"></td>
        </tr>
        <tr>
            <td align="right"><b>cardholdername</b></td>
            <td><input type="text" value="Jane Doe" id="cc_moto_cardholdername"></td>
        </tr>
        <tr>
            <td align="right"><b>issueMonth</b></td>
            <td><input type="text" value="01" id="cc_moto_issueMonth"></td>
        </tr>
        <tr>
            <td align="right"><b>issueYear</b></td>
            <td><input type="text" value="2013" id="cc_moto_issueYear"></td>
        </tr>
        <tr>
            <td align="right"><b>issueNumber</b></td>
            <td><input type="text" value="1234" id="cc_moto_issueNumber"></td>
        </tr>
        <tr>
            <td colspan="2" align="right"><input type="button" value="Store CreditCardMoto data" onClick="storeData('CreditCardMoto');"></td>
        </tr>
    <?php } else { ?>
        <tr>
            <td colspan="2">
                <div id="creditcardmotoDataIframe"></div>
            </td>
        </tr>
        <script type="text/javascript">
            var wd = new WirecardCEE_DataStorage();
            wd.buildIframeCreditCardMoto('creditcardmotoDataIframe', '100%', '200px');
        </script>
        <tr>
            <td colspan="2">
                <i>The HTML containing the input fields for sensitive credit card data is hosted at Wirecard.
                    Based on Javascript the consumer input is immediately send to Wirecard, validated and stored in the data storage,
                    therefore no button for storing this credit card data is required.</i>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="2" align="center"><b>Maestro SecureCode</b></td>
    </tr>
    <?php if (!$PCI3_DSS_SAQ_A_ENABLE) { ?>
        <tr>
            <td align="right"><b>pan</b></td>
            <td><input type="text" value="9500000000000001" id="maestro_pan"></td>
        </tr>
        <tr>
            <td align="right"><b>expirationMonth</b></td>
            <td><input type="text" value="12" id="maestro_expirationMonth"></td>
        </tr>
        <tr>
            <td align="right"><b>expirationYear</b></td>
            <td><input type="text" value="2031" id="maestro_expirationYear"></td>
        </tr>
        <tr>
            <td align="right"><b>cardverifycode</b></td>
            <td><input type="text" value="123" id="maestro_cardverifycode"></td>
        </tr>
        <tr>
            <td align="right"><b>cardholdername</b></td>
            <td><input type="text" value="Jane Doe" id="maestro_cardholdername"></td>
        </tr>
        <tr>
            <td align="right"><b>issueMonth</b></td>
            <td><input type="text" value="01" id="maestro_issueMonth"></td>
        </tr>
        <tr>
            <td align="right"><b>issueYear</b></td>
            <td><input type="text" value="2013" id="maestro_issueYear"></td>
        </tr>
        <tr>
            <td align="right"><b>issueNumber</b></td>
            <td><input type="text" value="1234" id="maestro_issueNumber"></td>
        </tr>
        <tr>
            <td colspan="2" align="right"><input type="button" value="Store Maestro data" onClick="storeData('Maestro');"></td>
        </tr>
    <?php } else { ?>
        <tr>
            <td colspan="2">
                <div id="maestroDataIframe"></div>
            </td>
        </tr>
        <script type="text/javascript">
            var wd = new WirecardCEE_DataStorage();
            wd.buildIframeMaestro('maestroDataIframe', '100%', '200px');
        </script>
        <tr>
            <td colspan="2">
                <i>The HTML containing the input fields for sensitive maestro card data is hosted at Wirecard.
                    Based on Javascript the consumer input is immediately send to Wirecard, validated and stored in the data storage,
                    therefore no button for storing this maestro card data is required.</i>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="2" align="center"><b>SEPA Direct Debit</b></td>
    </tr>
    <tr>
        <td align="right"><b>bankBic</b></td>
        <td><input type="text" value="ABC1234567" id="sepa-dd_bankBic"></td>
    </tr>
    <tr>
        <td align="right"><b>bankAccountIban</b></td>
        <td><input type="text" value="1234123412341234" id="sepa-dd_bankAccountIban"></td>
    </tr>
    <tr>
        <td align="right"><b>accountOwner</b></td>
        <td><input type="text" value="John Doe" id="sepa-dd_accountOwner"></td>
    </tr>
    <tr>
        <td colspan="2" align="right"><input type="button" value="Store SEPA Direct Debit data" onClick="storeData('SEPA-DD');"></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><b>giropay</b></td>
    </tr>
    <tr>
        <td align="right"><b>accountOwner</b></td>
        <td><input type="text" value="Jane Doe" id="giropay_accountOwner"></td>
    </tr>
    <tr>
        <td align="right"><b>bankAccount</b></td>
        <td><input type="text" value="1234567890" id="giropay_bankAccount"></td>
    </tr>
    <tr>
        <td align="right"><b>bankNumber</b></td>
        <td><input type="text" value="99000001" id="giropay_bankNumber"></td>
    </tr>
    <tr>
        <td colspan="2" align="right"><input type="button" value="Store giropay data" onClick="storeData('giropay');"></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><b>paybox</b></td>
    </tr>
    <tr>
        <td align="right"><b>payerPayboxNumber</b></td>
        <td><input type="text" value="0123456789" id="payerPayboxNumber"></td>
    </tr>
    <tr>
        <td colspan="2" align="right"><input type="button" value="Store paybox data" onClick="storeData('paybox');"></td>
    </tr>
    <tr>
        <td colspan="2" align="center"><b>Voucher</b></td>
    </tr>
    <tr>
        <td align="right"><b>voucherId</b></td>
        <td><input type="text" value="0123456789" id="voucherId"></td>
    </tr>
    <tr>
        <td colspan="2" align="right"><input type="button" value="Store Voucher data" onClick="storeData('voucher');"></td>
    </tr>
</table>

<h2>Step 3: Reading sensitive payment specific data from the Wirecard data storage</h2>

<p>
    For testing purposes you can read the previously stored sensitive data of various payment types in
    the Wirecard data storage.
    (The reading of this sensitive payment data is done server-side via PHP.)
</p>

<table border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
    <tr>
        <td colspan="2" align="right">
            <input type="button" value="Read Wirecard data storage"
                   onClick="javascript:window.open('frontend/read_datastorage.php', 'read_datastorage_window', 'width=1000,height=800,scrollbars=yes');">
        </td>
    </tr>
</table>

<h2>Step 4: Starting the payment process</h2>

<p>
    For testing purposes you can simulate the payment process within the demo mode.
</p>

<iframe src="frontend/start.php" width="820" height="400" name="<?php echo $CHECKOUT_WINDOW_NAME; ?>"></iframe>

<h2>Step 5: Handling the confirmation</h2>

<p>
    For testing purposes the confirmation (frontend/confirm.php) saves the return parameters to a text file (payment/confirm.txt).
</p>

<table border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
    <tr>
        <td colspan="2" align="right">
            <input type="button" value="Show confirmation result"
                   onClick="javascript:window.open('frontend/confirm_info.txt', 'confirm_text_window', 'width=640,height=480');">
        </td>
    </tr>
</table>

<h2>Step 6: Re-Initializing the Wirecard data storage</h2>

<p>
    After starting the payment process the data storage id is not valid anymore. To start a new payment process you need to re-initialize the
    Wirecard data storage.
</p>

<table border="1" bordercolor="lightgray" cellpadding="10" cellspacing="0">
    <tr>
        <td colspan="2" align="right">
            <input type="button" value="Re-initialize Wirecard data storage"
                   onClick="javascript:window.location.reload(-1);">
        </td>
    </tr>
</table>

</body>
</html>
