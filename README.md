# caridea-auth
Caridea is a miniscule PHP application library. This shrimpy fellow is what you'd use when you just want some helping hands and not a full-blown framework.

![](http://libreworks.com/caridea-100.png)

This is its authentication component. It provides a way to authenticate principals and store their identity. It will broadcast authentication events for any listeners. It works with any implementation of [PSR-7](http://www.php-fig.org/psr/psr-7/).

Included are three adapters for authentication through MongoDB, PDO, and X.509 client SSL certificates. You can easily write your own adapter for other authentication sources like IMAP, LDAP, or OAuth2.

[![Build Status](https://travis-ci.org/libreworks/caridea-auth.svg)](https://travis-ci.org/libreworks/caridea-auth)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/libreworks/caridea-auth/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/libreworks/caridea-auth/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/libreworks/caridea-auth/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/libreworks/caridea-auth/?branch=master)

## Installation

You can install this library using Composer:

```console
$ composer require caridea/auth
```

This project requires PHP 5.5 and depends on `caridea/event`, `caridea/session`, `psr/log`, and `psr/http-message`.

## Compliance

Releases of this library will conform to [Semantic Versioning](http://semver.org).

Our code is intended to comply with [PSR-1](http://www.php-fig.org/psr/psr-1/), [PSR-2](http://www.php-fig.org/psr/psr-2/), and [PSR-4](http://www.php-fig.org/psr/psr-4/). If you find any issues related to standards compliance, please send a pull request!

## Examples

Just a few quick examples.

### Login

```php
// Let's say $session is a \Caridea\Session\Session, such as \Caridea\Session\NativeSession
// Let's say $publisher is a \Caridea\Event\Publisher, such as \Caridea\Container\Objects
$service = new \Caridea\Auth\Service($session, $publisher);

// Let's say $collection is a \MongoCollection
$adapter = new \Caridea\Auth\Adapter\Mongo($collection, 'username', 'password');

// Let's say $request is a \Psr\Http\Message\RequestInterface
if ($service->login($request, $adapter)) {
    $principal = $service->getPrincipal();
    $username = $principal->getUsername();
    $details = $principal->getDetails());

    // $details = [
    //    'id' => '1234567890',
    //    'ua' => 'Mozilla/5.0',
    //    'ip' => '192.168.1.1'
    // ];
}
```

Upon login, `Service` will broadcast a `Caridea\Auth\Event\Login` if `$publisher` has been set.

### Resume

```php
// Let's say $session is a \Caridea\Session\Session, such as \Caridea\Session\NativeSession
// Let's say $publisher is a \Caridea\Event\Publisher, such as \Caridea\Container\Objects
$service = new \Caridea\Auth\Service($session, $publisher);

if ($service->resume()) {
    $principal = $service->getPrincipal();
}
```

Upon resume, `Service` will broadcast a `Caridea\Auth\Event\Resume` if `$publisher` has been set.

### Logout

```php
// Let's say $session is a \Caridea\Session\Session, such as \Caridea\Session\NativeSession
// Let's say $publisher is a \Caridea\Event\Publisher, such as \Caridea\Container\Objects
$service = new \Caridea\Auth\Service($session, $publisher);

// Let's say $collection is a \MongoCollection
$adapter = new \Caridea\Auth\Adapter\Mongo($collection, 'username', 'password');

if ($service->logout()) {
    // anonymous!
}
```

Upon login, `Service` will broadcast a `Caridea\Auth\Event\Logout` if `$publisher` has been set.

### Login Timeout

A component has been included, the `TimeoutListener` which can be registered in a `Caridea\Event\Publisher`. 

It listens for `Caridea\Auth\Event\Resume` and will log out a user if an authenticated session has either gone on too long or has been idle for too long.
