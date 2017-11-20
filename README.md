# Monzo PHP bindings

[![Build Status](https://travis-ci.org/ameliaikeda/monzo-php.svg?branch=master)](https://travis-ci.org/ameliaikeda/monzo-php)

This library allows access to the [Monzo](https://monzo.com) API in PHP. This library requires PHP 7.1+.

## Installation

```
composer require amelia/monzo-php
```

If you don't already have your own access tokens from completing oauth yourself, you'll need to also `composer require laravel/socialite`.

You should set `MONZO_CLIENT_ID` and `MONZO_CLIENT_SECRET` environment variables to values that you get from creating an application at [https://developers.monzo.com](https://developers.monzo.com).

## Laravel integration (5.4+)

Stick `Amelia\Monzo\MonzoServiceProvider::class` in `app.php`.

After that, configure the package using `php artisan monzo:install`.

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
If not using laravel, you'll need to set up a singleton instance of `Amelia\Monzo\Monzo` and inject an `Amelia\Monzo\Contracts\Client` instance into it, as follows:

```php
<?php

$client = Amelia\Monzo\ClientFactory::make(
    getenv('MONZO_CLIENT_ID'),
    getenv('MONZO_CLIENT_SECRET')
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

$user = App\User::findOrFail($id);

$accounts = $monzo->as($user)->accounts();
```

### Grab the last 100 transactions for a user account

```php
<?php

$user = App\User::findOrFail($id);

$transactions = $monzo->as($user)->transactions('acc_12341243');
```

### Grab the last 100 transactions for a user's default account

```php
<?php

$user = App\User::findOrFail($id);

// will query accounts first, then use the default to query transactions.
$transactions = $monzo->as($user)->transactions();
```

### Grab a paginator instance for a user's transactions

```php
<?php

$user = App\User::findOrFail($id);

$transactions = $monzo->as($user)->paginate(50)->transactions('acc_12341243');
```

### Expand (and hydrate) relations in the API

```php
<?php

$user = App\User::findOrFail($id);

$transactions = $monzo->as($user)
    ->paginate(50)
    ->expand('account')
    ->transactions('acc_12341243');
```

### See a user's balance

```php
<?php

$user = App\User::findOrFail($id);

$balance = $monzo->as($user)->balance();
```

