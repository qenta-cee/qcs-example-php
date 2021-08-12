<?php
/**
 * QMore Checkout Seamless Demo
 * - Terms of use can be found under
 * https://guides.qenta.com/prerequisites
 * - License can be found under:
 * https://github.com/qenta-cee/qcs-example-php/blob/master/LICENSE.
 */


// Please put this file in a folder where it is not possible to access it
// from a web browser via your web server.

// customer id
// e.g. D200001 for demonstration purposes only
// for production mode, please use your personal customer id obtained by QENTA
$customerId = getenv('QCS_CUSTOMER_ID') ?: "D200001";

// secret
// pre-shared key, used to sign the transmitted data
// e.g. B8AKTPWBRMNBV455FG6M2DANE99WU2 for testing purposes
// for production mode, please use your personal secret obtained by QENTA
$secret = getenv('QCS_SHOP_SECRET') ?: "B8AKTPWBRMNBV455FG6M2DANE99WU2";

// shop id
// please use this parameter only if it is enabled by QENTA
$shopId = getenv('QCS_SHOP_ID') ?: "seamless";

// session variable name for storing the id of the QENTA Data Storage
$STORAGE_ID = "QENTA_dataStorageId";

// URLs for accessing the QENTA Checkout Platform
$URL_QENTA_CHECKOUT = getenv('QCS_ENDPOINT') ?: "https://api.qenta.com";
$URL_DATASTORAGE_INIT = $URL_QENTA_CHECKOUT . "/qmore/dataStorage/init";
$URL_DATASTORAGE_READ = $URL_QENTA_CHECKOUT . "/qmore/dataStorage/read";
$URL_FRONTEND_INIT = $URL_QENTA_CHECKOUT . "/qmore/frontend/init";
$QENTA_CHECKOUT_PORT = getenv('QCS_CHECKOUT_PORT') ?: 443;
$QENTA_CHECKOUT_PROTOCOL = getenv('QCS_CHECKOUT_PROTO') ?: CURLPROTO_HTTPS;

// name of iFrame containing the checkout
$CHECKOUT_WINDOW_NAME = "qenta_checkout";

// URL of this webpage
$WEBSITE_URL = getBaseUrl();

// ---------------------------------------------------------------------------
// Configuration for PCI3 DSS SAQ-A compliant behaviour for
// entering credit card data

// true, to enable compliance to PCI3 DSS SAQ-A where the web page containing the
// input fields for credit card data is delivered by QENTA and not via
// the web server of the merchant
$PCI3_DSS_SAQ_A_ENABLE = false;

// URL for style sheet to format the credit card input fields as delivered by QENTA
// please be aware that @import, url() are ignored for security reasons
//$PCI3_DSS_SAQ_A_IFRAME_CSS_URL = null; // no styling
//$PCI3_DSS_SAQ_A_IFRAME_CSS_URL = $WEBSITE_URL . 'frontend/style/pci3_iframe_style1.css'; // simple style which sets input fields into new row
$PCI3_DSS_SAQ_A_IFRAME_CSS_URL = $WEBSITE_URL . 'frontend/style/pci3_iframe_style2.css'; // extended styling of all elements

// optional input fields, possible values are null (means default), true, false
$PCI3_DSS_SAQ_A_CCARD_SHOW_CVC = true;
$PCI3_DSS_SAQ_A_CCARD_SHOW_CARDHOLDERNAME = true;
$PCI3_DSS_SAQ_A_CCARD_SHOW_ISSUEDATE = false;
$PCI3_DSS_SAQ_A_CCARD_SHOW_ISSUENUMBER = false;

//--------------------------------------------------------------------------------//

