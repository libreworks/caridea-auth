# Events

If the user has supplied a `Caridea\Event\Publisher`, such as `Caridea\Container\Objects`, the `Caridea\Auth\Service` class broadcasts events during the lifecycle of an authentication.

## Login

When the `login` method is invoked, a `Caridea\Auth\Event\Login` event is published, with a `source` of the service.

## Resume

When the `resume` method is invoked, a `Caridea\Auth\Event\Resume` event is published, with a `source` of the service. The event class has two methods of note:

* `getFirstActive` – Returns the `float` time when the authentication first occurred, as returned by `microtime(true)`.
* `getLastActive` – Returns the `float` time of the most recent session resume, as returned by `microtime(true)`.

## Logout

When the `logout` method is invoked, a `Caridea\Auth\Event\Logout` event is published, with a `source` of the service.

## Timeout Listener

The `Caridea\Auth\TimeoutListener` class is a `Caridea\Event\Listener` that can be registered with a `Caridea\Event\Publisher`. It listens for `Caridea\Auth\Event\Resume` notifications.

The `TimeoutListener` has two optional constructor parameters:
* The number of seconds until a session should be considered idle. If omitted, the default is 20 minutes.
* The number of seconds until a session should be considered expired. If omitted, the default is 24 hours.

```php
$idle = 2400 // 40 minutes
$expired = 43200 // 12 hours
$listener = new \Caridea\Auth\TimeoutListener($idle, $expired);
```

When the listener is notified of a resume event, the following happens:

* If the difference between the current time and the value returned by `getLastActive` is more than the idle time, the `logout` service method is invoked.
* If the difference between the current time and the value returned by `getFirstActive` is more than the expiration time, the `logout` service method is invoked.
