# PHP WEB Profiler

Motivation (or `why one more PHP debug bar?`): create a tiny profiling bar to use in multiple projects (not just a PHP
backend). It should have no dependencies, easy integration into legacy projects, a small footprint, fast, and low
overhead to run in production environments. Current solutions were not able to satisfy one or more requirements.

This library is a PHP backend to [WEB Profiler UI](https://github.com/kirik/web-profiler-ui) and uses it as a frontend.
Please refer to [UI docs](https://github.com/kirik/web-profiler-ui/blob/main/README.md) to get more information
regarding internals, features, and other perks.

[Click here](http://kirik.github.io/web-profiler-ui/) to see it in action.

![Docked mode](https://raw.githubusercontent.com/kirik/web-profiler-ui/main/doc/docked_mode.png "Docked mode")

## Requirements

Are specified in composer.json:

- PHP 7+ (as we have a tiny amount of code, we can easily make it work with PHP 5+)
- [web-profiler-ui](https://github.com/kirik/web-profiler-ui)
- That's pretty much it...

## Installation

1. Install the package with the command:

```shell
composer require kirik/web-profiler-php
```

_As you can see, there is no `--dev` flag; that means we will be able to profile our pages on production too ;)_

2. Embed the profiler into your project (please see [example/main.php](example/main.php)):

```php
// include composer autoloader
require('vendor/autoload.php');

// right after including composer, start profiling
\WebProfilerPhp\Profiler::start($_SERVER['REQUEST_URI'], []);

// application code
// ----

// at the very end of the application lifecycle, stop the profiler and render UI
echo \WebProfilerPhp\Profiler::render([]);
```

Until `Profiler::start` is called, no spans are being collected, and `Profiler::render` will return an empty string.
Keeping that in mind, you can safely run it on production with almost no overhead on top. Personally, I prefer to use a
cookie+IP condition to enable profiling:

```php
if (isset($_COOKIE['__profiler']) && $_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
    \WebProfilerPhp\Profiler::start($_SERVER['REQUEST_URI'], []);
}
```

and then use a browser bookmark to toggle the profiler:

```text
javascript:(function(){const parts=`; ${document.cookie}`.split('; __profiler=');let enabled=(parts.length===2)?parseInt(parts.pop().split(';').shift()):0;document.cookie='__profiler='+(enabled>0?'0':'1')+'; path=/';if(confirm('Profiler has been '+(enabled>0?'disabled':'enabled')+'. Reload this page?')){document.location.reload();}})();
```

3. Add collectors/proxies to your application. Please refer to [example/main.php](example/main.php).
   Supported [collectors](#writing-your-own-collector):

- Database - used by PDO proxy, but you can use it on your own
- Log - just a simple logger
- PhpInfo - prints phpinfo() to the profiler

Base example of calling the Database collector:

```php
$span = \WebProfilerPhp\Collector\Database::start('SELECT * FROM users WHERE id = 123');

// run query

// stopping span PHP7 style  
if ($span !== null) {
    $span->stop(1);
}
// stopping span PHP8+ style using the null-safe operator  
$span?->stop(1);
```

Supported [proxies](#proxies):

- PDO

4. add profiler response to your AJAX responses

```php
$ajaxResponse = [/*some application response*/];

// adding __profiler key to the response (NOTE this will be added ONLY if the profiler was started)
$ajaxResponse = \WebProfilerPhp\Profiler::addProfilerToJson($ajaxResponse);

echo json_encode($ajaxResponse);
```

5. You're awesome!

## Internals

The proxy is used to integrate well-known libraries and frameworks; PDO is the only supported proxy yet. Please don't
hesitate to PR your proxy.

The collector is the entity that is used to measure/log events; they can be easily inherited/extended to provide your
own metric.

### Proxies

#### PDO

Use `\WebProfilerPhp\Proxy\PDO` class as a proxy class for [PDO](https://www.php.net/manual/en/book.pdo.php).

```php
$dbh = new \WebProfilerPhp\Proxy\PDO($dsn, $user, $password);
// ...
```

### Writing your own collector

Please extend `\WebProfilerPhp\Collector\Base` and override properties. You can also expand Database or Log
collectors to implement the same logic. Please refer to [example/own_collectors.php](example/own_collectors.php);

## Roadmap

- [ ] add Guzzle proxy
- [ ] add MongoDB proxy
- [ ] add ClickHouse proxy
- [ ] add frameworks support (Laravel, Symfony, etc...)

# Alternatives

- [PHP Debug Bar](https://github.com/maximebf/php-debugbar)
- [Symfony web profiler](https://symfony.com/doc/current/profiler.html)
- [Laravel Telescope Dev Toolbar](https://laravel-news.com/laravel-telescope-dev-toolbar)
- [Drupal Debug Bar](https://www.drupal.org/project/debug_bar)
