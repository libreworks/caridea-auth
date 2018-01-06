# Working with the Service

The `Caridea\Auth\Service` class is your primary means of interacting with this library.

When constructed, the `Caridea\Auth\Service` needs to be provided a `Caridea\Session\Session`. It optionally accepts a `Caridea\Event\Publisher` and a `Caridea\Auth\Adapter`. It also implements `Caridea\Event\PublisherAware` and `Psr\Log\LoggerAwareInterface`.

```php
$session = \Caridea\Session\NativeSession($_COOKIE);
$manager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
$adapter = new \Caridea\Auth\Adapter\MongoDb($manager, 'collection_foobar', 'username', 'password');
$service = new \Caridea\Auth\Service($session, null, $adapter);
$service->setLogger(new \Psr\Log\NullLogger());
```

## The Principal

The currently authenticated user is represented by the `Caridea\Auth\Principal` class. It contains three methods of note.

* `getUsername` – Gets the `string` username of the authenticated principal, or `null` if the principal is anonymous
* `getDetails` – Gets an associative `array` of details about the authentication
* `isAnonymous` – Returns `true` if the principal is anonymous, `false` otherwise

## Service Methods

The `Caridea\Auth\Service` class contains only a few public methods.

* `getPrincipal` – This returns the currently authenticated `Caridea\Auth\Principal`, which could be anonymous
* `login` – Uses an adapter to authenticate a principal using details from the request; returns `true` if successful, throws exceptions otherwise
* `resume` – Resumes an authenticated session; returns `true` if one existed, `false` otherwise
* `logout` – Ends an authenticated session; returns `true` if one existed, `false` otherwise

### Login

The `login` method must be provided a PSR-7 `RequestInterface` that it uses to retrieve the credentials entered by the user. An optional second argument is the adapter to use, which is required if one was not specified when the service was constructed.

```php
// Let's say $request is a \Psr\Http\Message\RequestInterface
if ($service->login($request)) {
    $principal = $service->getPrincipal();
    $username = $principal->getUsername();
    $details = $principal->getDetails());
    var_dump($details);
}
```

This might output:

```
array(3) {
  'id' =>
  string(10) "1234567890"
  'ua' =>
  string(11) "Mozilla/5.0"
  'ip' =>
  string(11) "192.168.1.1"
}
```

Once `login` is invoked, the authenticated principal is stored in the session and a message about the authentication is logged using the `info` level.

### Resume

If a principal has been previously authenticated in the active session, the `resume` method will pick it back up.

```php
if ($service->resume()) {
    $principal = $service->getPrincipal();
}
```

If an authentication is resumed successfully, a message about the resumption is logged using the `info` level.

### Get the Principal

The first time the `getPrincipal` method is invoked, it will attempt to invoke the `resume` method if it hasn't already been called. If no authenticated principal is available, it will return an anonymous principal.

### Logout

Invoking the `logout` method will destroy the active session and reset the principal to be anonymous.

```php
if ($service->logout()) {
    // anonymous!
}
```

if an authentication is logged out successfully, a message about the logout is logged using the `info` level.
