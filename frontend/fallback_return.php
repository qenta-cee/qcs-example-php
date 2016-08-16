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

$response = isset($_POST['response']) ? $_POST['response'] : '';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <script type="text/javascript">
        function setResponse(response) {
            if (typeof parent.WirecardCEE_Fallback_Request_Object == 'object') {
                parent.WirecardCEE_Fallback_Request_Object.setResponseText(response);
            }
            else {
                console.log('Not a valid fallback call.');
            }
        }
    </script>
</head>
<body onload='setResponse("<?php echo addslashes($response); ?>");'>
</body>
</html>
