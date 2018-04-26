# auth-abstractor

[![Build Status](https://travis-ci.org/matthewslouismarie/auth-abstractor.svg?branch=master)](https://travis-ci.org/matthewslouismarie/auth-abstractor)

A PHP library which aims to completely abstract the authentication logic
from your PHP web application. You won't even have to create the views!

It does so by acting as a middleware. Simply pass it the HTTP request and you
will get back an HTTP response along with an object of the class
AuthenticationProcess.

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



### Creating an `AuthenticationKernel` object

You need to construct an [AuthenticationKernel](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Controller.AuthenticationKernel.html) by passing an implementation of [IApplicationConfiguration](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Configuration.IApplicationConfiguration.html) to its constructor. You are not obliged to define your own
implementation of `IApplicationConfiguration` however. Instead, you can also
simply pass it a `ApplicationConfiguration` object.

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

[IMember](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Model.IMember.html)
is an interface for members (users with an account) of your application. If you
already have a class that represents your members, you can simply make it
implements `IMember` as well. Otherwise, you can also use a [convenience
implementation](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Implementation.Member.html).

AuthenticationKernel is an object that can be
common to your entire web applications, so you can register it as a service if
your web application supports dependency injection (e.g. Symfony).

## Creating the Authentication Process

The first time the user arrives on a page, say the login page, the
authentication process does not exist. So you have to create it. It is advised
to use the [AuthenticationProcessFactory](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Factory.AuthenticationProcessFactory.html) to do that:

    $authProcess = (new AuthenticationProcessFactory())->createProcess([
            CredentialChallenge::class, // class that is part of auth-abstractor
        ]
    );

You pass to `createProcess` an array of challenge class names. A challenge is a
step in the authentication or registration process (e.g. a page asking for a
password, or a page asking for the user to plug their U2F device in). These
classes need to be implementations of [IChallenge](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Challenge.IChallenge.html).
You can define your owns of course. _auth-abstractor_ comes with the following challenges:
 - `CredentialChallenge`, for asking the user for their username and password,
 - `CredentialRegistrationChallenge`, for asking the user to create an account
 and give a valid username and password,
 - `ExistingUsernameChallenge`, for asking the user for a valid, existing
 username.
 - `PasswordChallenge`, for asking the user for their password,
 - `PasswordUpdateChallenge`, for asking the user to find a new password,
 - `U2fChallenge`, for asking the user to confirm their identity with their U2F
 device.
 - `U2fRegistrationChallenge`, for asking the user to register a new U2F device.

You can combine these (i.e. combine several of these in the array you pass to
`AuthenticationProcessFactory`. Sometimes, a certain order is necessary: e.g.
the username of the user must be known before `PasswordChallenge` gets
processed. One way to do that is to put a `ExistingUsernameChallenge` before.

[`AuthenticationProcessFactory` supports additional, optional parameters](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Factory.AuthenticationProcessFactory.html),
for example, to specify the current user's username.

# Processing the Authentication Process

You now need to call `processHttpRequest` of the AuthenticationKernel.

You pass it: a [PSR-7 representation of the HTTP request](https://www.php-fig.org/psr/psr-7/),
the created or retrieved authentication process, and a callback.

The callback needs to be an implementation of [IAuthenticationCallback](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Model.IAuthenticationCallback.html),
but you can simply instantiate a [Callback](https://matthewslouismarie.github.io/classes/LM.AuthAbstractor.Implementation.Callback.html) object.

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

[Symfony provides tools for converting the Response and Request to and from PSR-7
objects.](https://symfony.com/doc/current/components/psr7.html)

You can then store the new `AuthenticationProcess` somehow (e.g. in session) that
you will retrieve later instead of instantiating a new `AuthenticationProcess`
object. And of course, you return an HTTP response.

    // store new auth_process in session
    $_SESSION['auth_process'] = $response->getProcess();

    // display http response to user
    return $response->getHttpResponse();

[You can see a complete example of the use of _auth-abstractor_ here](https://github.com/matthewslouismarie/security-comparator/blob/41e6a420843d7aa6a00638bf98e1babde0aa2dba/symfony/src/Controller/TmpController.php#L38).

### Assets

In order for U2F registration and authentication to work, you will need
[google-u2f-api.js](https://www.npmjs.com/package/google-u2f-api.js) and
[jquery](https://www.npmjs.com/package/jquery). These files need to be in the
folder which path is given by [getAssetUri()](https://github.com/matthewslouismarie/auth-abstractor/blob/a97f0a64d5f0f8760d133f34afcf2a44ab1aa082/src/LM/Authentifier/Configuration/IApplicationConfiguration.php#L11).

Of course, you can override the U2F views with your very own views which can
use different JavaScript libraries.

## API

You can browse _auth-abstractor_'s API [here](https://matthewslouismarie.github.io).