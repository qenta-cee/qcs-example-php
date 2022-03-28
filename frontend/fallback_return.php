<?php
/**
 * QMore Checkout Seamless Demo
 * - Terms of use can be found under
 * https://guides.qenta.com/prerequisites
 * - License can be found under:
 * https://github.com/qenta-cee/qcs-example-php/blob/master/LICENSE.
 */
$response = isset($_POST['response']) ? $_POST['response'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript">
        function setResponse(response) {
            if (typeof parentec.QentaCEE_Fallback_Request_Object == 'object') {
                parent.QentaCEE_Fallback_Request_Object.setResponseText(response);
                console.log(response);
            }
            else {
                console.log('Not a valid fallback call.');
            }
        }
    </script>
</head>
<body id="bodyReturn" onload='setResponse("<?php echo addslashes(htmlspecialchars($response)); ?>");'>
  <div id="contentReturn">
  </div>
</body>
</html>
