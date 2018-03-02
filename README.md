<h1 align="center">Monzo PHP Client</h1>
<p align="center">
<a href="https://travis-ci.org/ameliaikeda/monzo-php">
    <img src="https://travis-ci.org/ameliaikeda/monzo-php.svg?branch=master" alt="">
</a>
<a href="https://scrutinizer-ci.com/g/ameliaikeda/monzo-php/?branch=master">
    <img src="https://scrutinizer-ci.com/g/ameliaikeda/monzo-php/badges/quality-score.png?b=master" alt="">
</a>
<a href="https://styleci.io/repos/82849326">
    <img src="https://styleci.io/repos/82849326/shield?branch=master" alt="">
</a>
</p>
<p align="center">This library allows access to the <a href="https://monzo.com">Monzo</a> API in PHP. This library requires PHP 7.1+.</p>


## Installation

```
composer require amelia/monzo-php
```

If you don't already have your own access tokens from completing oauth yourself, you'll need to also `composer require laravel/socialite`.

You should set the following variables in your `.env` (or otherwise):

- `MONZO_CLIENT_ID`
- `MONZO_CLIENT_SECRET`
- `MONZO_REDIRECT_URI`

You can create an application at [https://developers.monzo.com](https://developers.monzo.com).

## Laravel integration

`Amelia\Monzo\MonzoServiceProvider::class` is registered automatically in Laravel 5.5.

A future version of this package will include automatic webhook handling per-user, and full automatic socialite integration.

The environment variables that control these will be:

- `MONZO_WEBHOOKS=true`
- `MONZO_SOCIALITE=true`

### Socialite integration

To automatically add callbacks for socialite, this package provides an optional authentication system.

> **Caveat**
> This assumes you are adding existing users to an app on monzo.
> If you are not doing this, you'll need to set up your own routes to create/manage users based on API responses from socialite.

First, add the `MonzoCredentials` trait to your `Authenticatable` user model.

```php
<?php

namespace App;

use Amelia\Monzo\MonzoCredentials;
use Amelia\Monzo\Contracts\HasMonzoCredentials;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements HasMonzoCredentials
{
    use MonzoCredentials;
}
```

This adds a bunch of setters/getters to your user model for handling monzo credentials.

You can customise the columns used by adding methods to your user model:

```php
<?php

use Amelia\Monzo\MonzoCredentials;
use Amelia\Monzo\Contracts\HasMonzoCredentials;

class User implements HasMonzoCredentials {
    
    use MonzoCredentials;
    
    protected function getMonzoAccessTokenColumn()
    {
        return 'monzo_access_token';
    }

    protected function getMonzoRefreshTokenColumn()
    {
        return 'monzo_refresh_token';
    }

    protected function getMonzoUserIdColumn()
    {
        return 'monzo_user_id';
    }
}
```

### Socialite migrations

Assuming your users table is named `users`, you can simply run `php artisan vendor:publish --tag=monzo`.

This will create a migration in your `migrations` directory that can be edited.

Run `php artisan migrate` to run this.


## Usage

**Caveat**
If not using Laravel, you'll need to set up an instance of `Amelia\Monzo\Monzo` and inject an `Amelia\Monzo\Contracts\Client` instance into it, as follows:

```php
<?php

$client = new Amelia\Monzo\Client(
    new GuzzleHttp\Client,
    getenv('MONZO_CLIENT_ID') ?: null,
    getenv('MONZO_CLIENT_SECRET') ?: null
);

$monzo = new Amelia\Monzo\Monzo($client);

// Amelia\Monzo\Monzo::setAccessToken($token) for single user mode
```

**If using Laravel**, you only need to inject `Amelia\Monzo\Monzo` via the service container, using `resolve()` or `app()`.

Using the API is pretty simple.

In general, you'll need an access token or a user object.

## Examples


### Grab a user's accounts.

```php
<?php

$user = User::findOrFail($id);

$accounts = $monzo->as($user)->accounts();
```

### Grab the last 100 transactions for a user account

```php
<?php

$user = User::findOrFail($id);

$transactions = $monzo->as($user)->transactions('acc_12341243');
```

### Grab the last 100 transactions for a user's default account

```php
<?php

$user = User::findOrFail($id);

// will query accounts first, then use the default to query transactions.
$transactions = $monzo->as($user)->transactions();
```

### Grab a paginator instance for a user's transactions

```php
<?php

$user = User::findOrFail($id);

$transactions = $monzo->as($user)->paginate(50)->transactions('acc_12341243');
```

### Expand (and hydrate) relations in the API

```php
<?php

$user = User::findOrFail($id);

$transactions = $monzo->as($user)
    ->paginate(50)
    ->expand('account')
    ->transactions('acc_12341243');
```

### See a user's balance

```php
<?php

$user = User::findOrFail($id);

$balance = $monzo->as($user)->balance();
```

