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

  // checks if the parameter paymentState is available and set
  $paymentState = isset($_POST['paymentState']) ? $_POST['paymentState'] : 'undefined';

  // initiates the message text
  $message = 'The message text has not been set.';
  if (0 == strcmp($paymentState, 'CANCEL')) {
      $message = 'The payment transaction has been cancelled by the user.';
  } elseif (0 == strcmp($paymentState, 'PENDING')) {
      $message = 'The payment is pending and not yet finished.';
  } elseif (0 == strcmp($paymentState, 'FAILURE')) {
      // There was something wrong with the initiation or a
      // fatal error occured during the processing of the payment transaction.
      $message = 'There occured an error during the payment transaction.';
  } elseif (0 == strcmp($paymentState, 'SUCCESS')) {
      // The payment transaction has been completed successfully.

      // Collects fingerprint details for checking if response fingerprint
      // sent from QENTA is correct.
      $responseFingerprintOrder = isset($_POST['responseFingerprintOrder']) ? $_POST['responseFingerprintOrder'] : '';
      $responseFingerprint = isset($_POST['responseFingerprint']) ? $_POST['responseFingerprint'] : '';

      $fingerprintString = ''; // contains the values for computing the fingerprint
    $mandatoryFingerPrintFields = 0; // contains the number of received mandatory fields for the fingerprint
    $secretUsed = 0; // flag which contains 0 if secret has not been used or 1 if secret has been used
    $order = explode(',', $responseFingerprintOrder);
      for ($i = 0; $i < count($order); ++$i) {
          $key = $order[$i];
          $value = isset($_POST[$order[$i]]) ? $_POST[$order[$i]] : '';
          // checks if there are enough fields in the responsefingerprint
          if ((strcmp($key, 'paymentState')) == 0 && (strlen($value) > 0)) {
              ++$mandatoryFingerPrintFields;
          }
          if ((strcmp($key, 'orderNumber')) == 0 && (strlen($value) > 0)) {
              ++$mandatoryFingerPrintFields;
          }
          if ((strcmp($key, 'paymentType')) == 0 && (strlen($value) > 0)) {
              ++$mandatoryFingerPrintFields;
          }
          // adds secret to fingerprint string
          if (0 == strcmp($key, 'secret')) {
              $fingerprintString .= $secret;
              $secretUsed = 1;
          } else {
              // adds parameter value to fingerprint string
              $fingerprintString .= $value;
          }
      }

      // computes the fingerprint from the fingerprint string
      $fingerprint = hash_hmac('sha512', $fingerprintString, $secret);

      if ((0 == strcmp($fingerprint, $responseFingerprint))
    && (3 == $mandatoryFingerPrintFields)
    && (1 == $secretUsed)) {
          // everything is ok, please store the successfull payment in your system
          // please store at least the paymentType and the orderNumber additional
          // to the orderinformation, otherwise you will not be able to find the
          // transaction again
          // e.g. something like
          // checkBasketIntegrety($amount, $currency, $basketId);
          // storeAndCloseBasket($paymentType, $orderNumber, $basketId);

          $message = 'The payment has been successfully completed.';
      } else {
          // there is something strange, maybe an unauthorized
          // call of this page or a wrong secret
          $message = 'The verification of the response data was not successful.';
      }
  } else {
      // unauthorized call of this page
      $message = 'Error: The payment state is not valid.';
  }

  // creates a simple text file containing the confirmation details
  // of the latest checkout
  $file = fopen('confirm_info.txt', 'w');
  fwrite($file, date('Y-m-d H:i:s').' ');
  fwrite($file, $message."\n\n");
  fwrite($file, 'The QMore Checkout Seamless returned the following parameters:'."\n");
  foreach ($_POST as $key => $value) {
      if (0 == strcasecmp($key, 'submit_x') || 0 == strcasecmp($key, 'submit_y')) {
          // noop
      } else {
          fwrite($file, $key.' = '.$value."\n");
      }
  }
  fclose($file);

?>

