# APNS

[![Build Status](https://travis-ci.org/gepo/apns-http2.svg?branch=master)](https://travis-ci.org/gepo/apns-http2)
[![Dependency Status](https://www.versioneye.com/user/projects/57891926c3d40f003caa3071/badge.svg)](https://www.versioneye.com/user/projects/57891926c3d40f003caa3071)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gepo/apns-http2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gepo/apns-http2/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/gepo/apns-http2/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/gepo/apns-http2/?branch=master)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/gepo/apns-http2/blob/master/LICENSE)

PHP library for sending notifications via Apple Push Notification service (APNS) over HTTP/2.

# Installation

```
composer require gepo/apns-http2
```

# Requirements

## AAA Certificate

Since 29 March 2021, the AAA certificate is required. You can get it here: https://support.sectigo.com/Com_KnowledgeDetailPage?Id=kA03l00000117cL (the first one, name AAA).

On Ubuntu, you must put it in `/usr/local/share/ca-certificates/extra` (create the path if it doesn't exist), and run `update-ca-certificates` as `root`).

## cURL HTTP/2 support

You need cURL with HTTP/2 support installed on your system before work.

### Check if your installation supports it

Here's a simple script to check if your installation supports cURL with HTTP/2:

```php
<?php

if (!defined('CURL_HTTP_VERSION_2_0')) {
        define('CURL_HTTP_VERSION_2_0', 3);
}

$version = curl_version();
if ($version['features'] & constant('CURL_VERSION_HTTP2') !== 0) {
        echo 'HTTP/2 supported'.PHP_EOL;
} else {
        echo 'HTTP/2 not supported'.PHP_EOL;
}
```

### Installation on MacOS

To install it on OS X:
```
brew install curl --with-nghttp2 --with-openssl
brew link curl --force
brew reinstall php56 --with-homebrew-curl
```

# Usage

## Simple

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new \Apns\Client(__DIR__ . '/certs/apns-prod-cert.pem', true); // true is for sandbox

$message = (new \Apns\Message())
    ->setDeviceIdentifier('a00915e74d60d71ba3fb80252a5e197b60f2e7743f61b4411c713e9aabd2854f')
    ->setAlert('Test message')
    ->setTopic('com.mycompany.myapp')
;

$client->send($message);
```

## Complex

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Apns\Client as ApnsClient;
use Apns\Message as ApnsMessage;

$token = 'a00915e74d60d71ba3fb80252a5e197b60f2e7743f61b4411c713e9aabd2854f';

// Check if token format is valid
if ((!ctype_xdigit($token)) || (64 != strlen($token))) {
   die('Token is invalid!');
}

// Create client with production certificate and passphrase
$client = new ApnsClient(
    [
        __DIR__ . '/certs/apns-dev-cert.pem',
        'my-passphrase'
    ], 
    false // false is for production
);

// Get topic from certificate file
if ($cert = openssl_x509_parse(file_get_contents($apnsClient->getSslCert()[0]))) {
    $topic = $cert['subject']['UID'];
}

// Create message
$message = (new ApnsMessage())
    ->setDeviceIdentifier($token)
    ->setAlert('This is a test message sent on '.gmdate('r'))
    ->setData([
        'Key1' => 'Value1',
        'Key2' => 'Value2',
        'Key3' => 'Value3',
    ])
    ->setTopic($topic);

// Send it and catch errors
try {
    $client->send($message);
} catch (\Exception $e) {
    // https://developer.apple.com/library/archive/documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/CommunicatingwithAPNs.html#//apple_ref/doc/uid/TP40008194-CH11-SW17
    switch ($e->getMessage()) {
        case 'BadDeviceToken':
        case 'ExpiredProviderToken':
        case 'InvalidProviderToken':
        case 'MissingProviderToken':
        case 'Unregistered':
            // do something, ie. remove the token from your list
            break;
        case 'BadCollapseId':
        case 'BadExpirationDate':
        case 'BadMessageId':
        case 'BadPriority':
            // do something, ie. check your parameters
            break;
        case 'BadTopic':
        case 'MissingTopic':
        case 'DeviceTokenNotForTopic':
            // do something, ie. check that your topic is ok
            break;
        case 'BadCertificate':
            // do something, ie. check the certificate you provided
            break;
        case 'BadCertificateEnvironment':
            // do something, ie. check your certificate/environment (sandbox or production)
            break;
        case 'TooManyRequests':
        case 'TooManyProviderTokenUpdates':
            // do something, ie. throttle your requests
            break;
        default:
            // do something
            break;
    }
} 
```
