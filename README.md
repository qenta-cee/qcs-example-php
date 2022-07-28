# QMore Checkout Seamless integration example

[![License](https://img.shields.io/badge/license-GPLv3-blue.svg)](https://raw.githubusercontent.com/qenta-cee/qcp-example-php/master/LICENSE)
[![PHP v7.4](https://img.shields.io/badge/php-v7.4-green.svg)](https://www.php.net)

An example implementation for QMore Checkout Seamless in PHP.

This example demonstrates the integration principle of QMore Checkout Seamless and provides a basis for integration into PHP-based systems.

## Installation

This example needs a webserver with PHP-support on an externally reachable address for confirmation deliveries.

### Local installation
Required: Docker _and_ [docker-compose](https://docs.docker.com/compose/install/)

Recommended: [ngrok](https://ngrok.com)

Start via docker-compose:

`docker-compose up; docker-compose down`

Run ngrok:

`ngrok http 8000`

### Server or VPS
Copy the example code to a web server which supports PHP. Ensure that the web server is accessible from the Internet via port 80 (for http communication) or port 443 (for https communication). The web server needs a fully qualified domain name for receiving data from QENTA e.g. payment confirmations.


Our [Online Guides](https://guides.qenta.com/ "Online Guides") provide an in depth description of [QMore Checkout Seamless](https://guides.qenta.com/wcs/start "QMore Checkout Seamless").

## Support and additional information

If you have any questions or troubles to get this example up and running in your web server environment, please do not hesitate to contact our [support teams](https://guides.qenta.com/contact "support teams").
