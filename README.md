[![PHP version](https://badge.fury.io/ph/fphammerle%2Fyii2-client-cert-auth.svg)](https://badge.fury.io/ph/fphammerle%2Fyii2-client-cert-auth)
[![Build Status](https://travis-ci.org/fphammerle/yii2-client-cert-auth.svg?branch=master)](https://travis-ci.org/fphammerle/yii2-client-cert-auth)

## Setup

### 1. Configure Webserver

#### apache

```
<VirtualHost example.hammerle.me:443>
    # ...

    SSLEngine on
    SSLCertificateFile /etc/somewhere/example-server-cert.pem
    SSLCertificateKeyFile /etc/restricted/example-server-key.pem

    SSLVerifyClient optional
    SSLVerifyDepth 1
    SSLCACertificateFile /etc/somewhere/example-client-cert-ca.pem
    SSLOptions +StdEnvVars
</VirtualHost>
```

### 2. Install Extension

```
composer require fphammerle/yii2-client-cert-auth
```

### 3. Enable Extension in Yii's Application Config

```
$config = [
    // ...
    'bootstrap' => ['clientCertAuth'],
    'components' => [
        // ...
        'clientCertAuth' => \fphammerle\yii2\auth\clientcert\Authenticator::className(),
    ],
    // ...
];
```
