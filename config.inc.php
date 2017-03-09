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


// Please put this file in a folder where it is not possible to access it
// from a web browser via your web server.

// customer id
// e.g. D200001 for demonstration purposes only
// for production mode, please use your personal customer id obtained by Wirecard
$customerId = "D200001";

// secret
// pre-shared key, used to sign the transmitted data
// e.g. B8AKTPWBRMNBV455FG6M2DANE99WU2 for testing purposes
// for production mode, please use your personal secret obtained by Wirecard
$secret = "B8AKTPWBRMNBV455FG6M2DANE99WU2";

// shop id
// please use this parameter only if it is enabled by Wirecard
$shopId = "seamless";

// session variable name for storing the id of the Wirecard data storage
$STORAGE_ID = "Wirecard_dataStorageId";

// URLs for accessing the Wirecard Checkout Platform
$URL_WIRECARD_CHECKOUT = "https://checkout.wirecard.com";
$URL_DATASTORAGE_INIT = $URL_WIRECARD_CHECKOUT . "/seamless/dataStorage/init";
$URL_DATASTORAGE_READ = $URL_WIRECARD_CHECKOUT . "/seamless/dataStorage/read";
$URL_FRONTEND_INIT = $URL_WIRECARD_CHECKOUT . "/seamless/frontend/init";
$WIRECARD_CHECKOUT_PORT = 443;
$WIRECARD_CHECKOUT_PROTOCOL = CURLPROTO_HTTPS;

// name of iFrame containing the checkout
$CHECKOUT_WINDOW_NAME = "wirecard_checkout";

// URL of this webpage
$WEBSITE_URL = rtrim($_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'], '/') . '/';
$WEBSITE_URL = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://$WEBSITE_URL" : "http://$WEBSITE_URL";

// ---------------------------------------------------------------------------
// Configuration for PCI3 DSS SAQ-A compliant behaviour for
// entering credit card data

// true, to enable compliance to PCI3 DSS SAQ-A where the web page containing the
// input fields for credit card data is delivered by Wirecard and not via
// the web server of the merchant
$PCI3_DSS_SAQ_A_ENABLE = false;

// URL for style sheet to format the credit card input fields as delivered by Wirecard
// please be aware that @import, url() are ignored for security reasons
//$PCI3_DSS_SAQ_A_IFRAME_CSS_URL = null; // no styling
//$PCI3_DSS_SAQ_A_IFRAME_CSS_URL = $WEBSITE_URL . 'pci3_iframe_style1.css'; // simple style which sets input fields into new row
$PCI3_DSS_SAQ_A_IFRAME_CSS_URL = $WEBSITE_URL . 'pci3_iframe_style2.css'; // extended styling of all elements

// optional input fields, possible values are null (means default), true, false
$PCI3_DSS_SAQ_A_CCARD_SHOW_CVC = true;
$PCI3_DSS_SAQ_A_CCARD_SHOW_CARDHOLDERNAME = true;
$PCI3_DSS_SAQ_A_CCARD_SHOW_ISSUEDATE = false;
$PCI3_DSS_SAQ_A_CCARD_SHOW_ISSUENUMBER = false;

//--------------------------------------------------------------------------------//

