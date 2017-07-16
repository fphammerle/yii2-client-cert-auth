[![PHP version](https://badge.fury.io/ph/fphammerle%2Fyii2-client-cert-auth.svg)](https://badge.fury.io/ph/fphammerle%2Fyii2-client-cert-auth)
[![Build Status](https://travis-ci.org/fphammerle/yii2-client-cert-auth.svg?branch=master)](https://travis-ci.org/fphammerle/yii2-client-cert-auth)

## Setup

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
