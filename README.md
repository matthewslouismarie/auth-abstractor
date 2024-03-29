# auth-abstractor

[![Build Status](https://travis-ci.org/matthewslouismarie/auth-abstractor.svg?branch=master)](https://travis-ci.org/matthewslouismarie/auth-abstractor)
[![codecov](https://codecov.io/gh/matthewslouismarie/auth-abstractor/branch/master/graph/badge.svg)](https://codecov.io/gh/matthewslouismarie/auth-abstractor)
![Mutation Score](https://img.shields.io/badge/mutation%20score->70%25-brightgreen.svg)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/matthewslouismarie/auth-abstractor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/matthewslouismarie/auth-abstractor/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/matthewslouismarie/auth-abstractor/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)


    composer require matthewslouismarie/auth-abstractor


A PHP library which aims to completely abstract the authentication logic
from your PHP web application. You won't even have to create the views!

It does so by acting as a middleware. Simply pass it the HTTP request and you
will get back an HTTP response along with an object of the class
AuthenticationProcess.

## Documentation

You can browse _auth-abstractor_'s API documentation [here](https://matthewslouismarie.github.io/auth-abstractor/).

## Features

 - Web-framework-agnostic: _auth-abtractor_ can be used with any web-framework,
 or without any web-framework at all!
 - Simple to use: _auth-abstractor_ requires a minimal amount of code, even for
 handling entire authentication or registration processes (e.g. creating a
 registration page, or creating a login page).
 - Powerful features: _auth-abstractor_ almost entirely abstracts the
 authentication and registration process. Only a few lines of code from your
 side are sufficient to create a full-fledged registration page, login page,
 etc. _auth-abstractor_ takes care of displaying the views, hashing passwords,
 and verifying responses.
 - Customisable: _auth-abstractor_ can easily be customised and extended. More
 ways to authenticate the user can easily be added, and default views can be
 customised or entirely replaced.

## How to use it

### Overview

To use it, you first need to create an `AuthenticationKernel` object. This
object can be shared among your entire web application (and can be registered as
a service). Now, when the user arrives on a page, let's say a login page, you
need to create a new `AuthenticationProcess` object. You then pass this object,
along with the HTTP request, to the `AuthenticationKernel` object you created
earlier.
The `AuthenticationKernel` returns an HTTP response and a new 
`AuthenticationProcess`. You store the `AuthenticationProcess` somehow (e.g. in
session), and you send back to the user the HTTP response.

Note: in _auth-abstractor_, by authentication, I mean authentication and
registration.

_[security-comparator](https://github.com/matthewslouismarie/security-comparator)_ is a web application that makes use of _auth-abstractor_ to abstract the entirety of the registration and the authentication process.

You can even view [a one page example](https://github.com/matthewslouismarie/security-comparator/blob/master/symfony/src/Controller/TmpController.php) demonstrating the use of _auth-abstractor_ with
Symfony.



### Creating an `AuthenticationKernel` object

You need to construct an `AuthenticationKernel` by passing an implementation of `IApplicationConfiguration` to its constructor. You are not obliged to define your own
implementation of `IApplicationConfiguration` however. Instead, you can also
simply pass it a `ApplicationConfiguration` object.

```php
    $kernel = new AuthenticationKernel(new ApplicationConfiguration(
        'https://example.org', // HTTPS URL of your app (for U2F)
        'https://example.org/assets', // Assets base URL
        // This function is responsible for fetching members from the database.
        // It must return null if the member does not exist.
        function (string $username) use ($repo): ?IMember {
            return $repo->findOneBy([
                'username' => $username,
            ]);
        }
    ));
```

`IMember` is an interface for members (users with an account) of your application. If you
already have a class that represents your members, you can simply make it
implements `IMember` as well. Otherwise, you can also use the convenience
implementation `Member`.

`AuthenticationKernel` is an object that can be
common to your entire web applications, so you can register it as a service if
your web application supports dependency injection (e.g. Symfony).

### Creating the Authentication Process

The first time the user arrives on a page, say the login page, the
authentication process does not exist. So you have to create it. It is advised
to use the [AuthenticationProcessFactory](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Factory.AuthenticationProcessFactory.html) to do that:

```php
    $authProcess = $kernel
        ->getAuthenticationProcessFactory()
        ->createProcess([
            CredentialChallenge::class, // class that is part of auth-abstractor
        ]
    );
```

You pass to `createProcess` an array of challenge class names. A challenge is a
step in the authentication or registration process (e.g. a page asking for a
password, or a page asking for the user to plug their U2F device in). These
classes need to be implementations of `IChallenge`.
You can define your owns of course. _auth-abstractor_ comes with the following challenges:
 - `CredentialChallenge`, for asking the user for their username and password,
 - `CredentialRegistrationChallenge`, for asking the user to create an account
 and give a valid username and password,
 - `EmailChallenge`, for asking the user to enter a code sent to their email
 address.
 - `ExistingUsernameChallenge`, for asking the user for a valid, existing
 username.
 - `NamedU2fRegistrationChallenge`, same as `U2fChallenge`, but asks for a name,
 - `PasswordChallenge`, for asking the user for their password,
 - `PasswordUpdateChallenge`, for asking the user to find a new password,
 - `U2fChallenge`, for asking the user to confirm their identity with their U2F
 device.
 - `U2fRegistrationChallenge`, for asking the user to register a new U2F device.

You can combine these (i.e. combine several of these in the array you pass to
`AuthenticationProcessFactory`. Sometimes, a certain order is necessary: e.g.
the username of the user must be known before `PasswordChallenge` gets
processed. One way to do that is to put a `ExistingUsernameChallenge` before.

Each challenge relies on a certain numbers of parameters being defined. You are
pass the parameters when you create the authentication process using the
authentication process factory.

`AuthenticationProcessFactory` supports additional, optional parameters,
for example, to specify the current user's username.

### Processing the Authentication Process

You now need to call `processHttpRequest` of the AuthenticationKernel.

You pass it: a [PSR-7 representation of the HTTP request](https://www.php-fig.org/psr/psr-7/),
the created or retrieved authentication process, and a callback.

The callback needs to be an implementation of `IAuthenticationCallback`,
but you can simply instantiate a `Callback`.

```php
    $authResponse = $kernel->processHttpRequest(
        $httpRequest,
        $authProcess, // The $authProcess object just created or retrieved from session
        new Callback(
            function ($authProcess) { // if the user fails authenticating
                new Response('You tried too many login attempts!');
            },
            function ($authProcess) { // if the user succeeds logging in
                $_SESSION['logged_in'] = true;
                new Response('You\'re logged in!');
            }
        )
    );
```

[Symfony provides tools for converting the Response and Request to and from PSR-7
objects.](https://symfony.com/doc/current/components/psr7.html)

You can then store the new `AuthenticationProcess` somehow (e.g. in session) that
you will retrieve later instead of instantiating a new `AuthenticationProcess`
object. And of course, you return an HTTP response.

```php
    // store new auth_process in session
    $_SESSION['auth_process'] = $response->getAuthenticationProcess();

    // display http response to user
    return $response->getHttpResponse();
```

[You can see a complete example of the use of _auth-abstractor_ here](https://github.com/matthewslouismarie/security-comparator/blob/41e6a420843d7aa6a00638bf98e1babde0aa2dba/symfony/src/Controller/TmpController.php#L38).

### Persisting the changes

_auth-abstractor_ never changes your application directly. It does not know
whether what kind of DBMS you're using, or even if you use a database at all!
However, at some point, it needs to be able to tell you of changes you should
persist. For example, if you create an authentication process with the
`CredentialRegistrationChallenge`, you need to persist somewhere the member who
created their account!

The way to do that is simply to call getPersistOperation() on the
AuthenticationProcess object. From the callback's handleSuccessfulProcess()
method:

```php
    foreach ($authProcess->getPersistOperations() as $operation) {
        if ($operation->getType()->is(new Operation(Operation::CREATE))) {
            $member = $operation->getObject();
            if (is_a($member, IMember::class)) {
                // Saves $member in the database
            }
        }
    }
```

### Assets

In order for U2F registration and authentication to work, you will need
[google-u2f-api.js](https://www.npmjs.com/package/google-u2f-api.js) and
[jquery](https://www.npmjs.com/package/jquery). These files need to be in the
folder which path is given by [getAssetUri()](https://github.com/matthewslouismarie/auth-abstractor/blob/a97f0a64d5f0f8760d133f34afcf2a44ab1aa082/src/LM/Authentifier/Configuration/IApplicationConfiguration.php#L11).

Of course, you can override the U2F views with your very own views which can
use different JavaScript libraries.

# TODOs
 - Don't use hard-coded values to access the typed map
 - Challenges shouldn't reply on an implementation of IAuthenticationProcess
 - The library should be challenge-agnostic. (No part of the code should directly
 reference an IChallenge implementation, except classes used exclusively by
 challenges.) Currently, IApplicationConfiguration and IAuthenticationProcess
 are coupled with some IChallenge specifications.
 - Should challenges be able to access the typed map directly and modify it
 directly? The authentication handler could take care of this. This would
 mean challenges wouldn't need to worry about the validity of the typed map.
 But the keys and their associated types would need to be known by the auth
 handler.
 - Update Composer packages.