# Weblet Base Lib

[![Build Status](https://travis-ci.org/renegare/weblet-base.png?branch=0.1.x)](https://travis-ci.org/renegare/weblet-base)

This project contains a base set of classes that can be used to create a web application.
It is a library and should be included as dependency and extended to fit your needs.

The real base is Silex*, and is essentially a preconfigured Silex app.
The aim of providing the lowest possible learning curve in order to get started.

However, do not let that limit how ambitious you want your project to be ;).

The aims of this library is provide the following features:

* Functionally Testable architecture #WTH**
* Configuration
* Error Handling
* Cookie Session Management
* Logging Management
* Generic Healthcheck Endpoint (accessible outside of OAuth2 security)
* Url Generator (out of the box with Silex)

*(This list is not complete and will grow as the need requires)*

\* Using Silex v1.2.x (will be upgrading to 2.0 once stable)

\** \#WTH is Functionally Testable Architecture - This is admittedly a coined phrase to
describe integration testing of your app as a whole. This is not to be confused
with "End to End" or "Unit" testing (albeit PHPUnit is used as the test runner!). You are only ever testing your application and not
the internet.

## Documentation ...

... is none existent. However, there is at least one test for each feature. If they
are too complicated to read then I possibly need to rewrite it. But don't be scared.
Take a look an when I have time documentation will be a priority!

## Usage

To use this library, include it as a dependency in your project via composer

```
composer require renegare\weblet-base:dev-master
```
*(!!! Please use an actual tagged version for production use as ```dev-master```
will potentially be updated without warning and contain bugs. !!!)*

Then create an instance or extend the class ```\Mendeley\Social\Weblet```:

```
// Example index.php

/**
 * Pass the app name and configuration to the constructor
 * - No errors are thrown if any yaml file does not exist
 */
$app = new Renegare\Weblet\Base('weblet',
    __DIR__ . '/../app-constants.yml.dist',
    __DIR__ . '/../app-constants.yml',
    ['app.root' => dirname(__DIR__)] // required
);

// enable built in functionality
$app->enableCookieSession();
$app->enableLogging();

// register a route
$app->get('/', function(){
    return 'All Good!';
})->bind('homepage');

// and away we go!
$app->run();

```

## Test

```
composer test
```

## LICENSE

The MIT License (MIT)

Copyright (c) 2014 Renegare

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
