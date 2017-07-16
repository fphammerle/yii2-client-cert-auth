[![PHP version](https://badge.fury.io/ph/fphammerle%2Fyii2-client-cert-auth.svg)](https://badge.fury.io/ph/fphammerle%2Fyii2-client-cert-auth)
[![Build Status](https://travis-ci.org/fphammerle/yii2-client-cert-auth.svg?branch=master)](https://travis-ci.org/fphammerle/yii2-client-cert-auth)

## Setup

### 1. Install

```
composer require fphammerle/yii2-client-cert-auth
```

### 2. Yii Application Config

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
