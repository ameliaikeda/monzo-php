<?php

namespace Amelia\Test\Monzo;

use VCR\VCR;
use Carbon\Carbon;
use Amelia\Monzo\Monzo;
use Amelia\Monzo\Client;
use PHPUnit\Framework\TestCase;
use Amelia\Monzo\Models\Account;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Support\Collection;
use Amelia\Monzo\Models\Transaction;

class IntegrationTest extends TestCase
{
    protected static $user;
    protected static $token;
    protected static $account;
    protected static $integrationUser;
    protected static $integrationToken;
    protected static $integrationAccount;

    public function testAccountsEndpoint()
    {
        VCR::insertCassette('accounts');

        $monzo = new Monzo(new Client(new Guzzle));

        $accounts = $monzo->as(static::$token)->accounts();

        $this->assertInstanceOf(Collection::class, $accounts);
        $this->assertCount(1, $accounts);
        $this->assertContainsOnly(Account::class, $accounts);

        $account = $accounts->first();

        $this->assertEquals(static::$account, $account->id);
        $this->assertInstanceOf(Carbon::class, $account->created);
        $this->assertNotNull($account->description);
    }

    public function testTransactionsEndpoint()
    {
        VCR::insertCassette('transactions');

        $monzo = new Monzo(new Client(new Guzzle));

        $transactions = $monzo->as(static::$token)
            ->since('2016-01-01T00:00:00Z')
            ->take(25)
            ->transactions(static::$account);

        $this->assertInstanceOf(Collection::class, $transactions);

        $this->assertContainsOnly(Transaction::class, $transactions);
    }

    public function testDefaultTransactionsEndpoint()
    {
        VCR::insertCassette('transactions');

        $monzo = new Monzo(new Client(new Guzzle));

        $transactions = $monzo->as(static::$token)
            ->since('2016-01-01T00:00:00Z')
            ->take(25)
            ->transactions();

        $this->assertInstanceOf(Collection::class, $transactions);

        $this->assertContainsOnly(Transaction::class, $transactions);
    }

    public function testTransactionsEndpointLimiting()
    {
        VCR::insertCassette('transactions');

        $monzo = new Monzo(new Client(new Guzzle));

        $transactions = $monzo->as(static::$token)
            ->take(20)
            ->since('2016-01-01T00:00:00Z')
            ->transactions(static::$account);

        $this->assertInstanceOf(Collection::class, $transactions);

        $this->assertCount(20, $transactions);

        $this->assertContainsOnly(Transaction::class, $transactions);
    }

    public function testTransactionsEndpointLimitingWithoutAccount()
    {
        VCR::insertCassette('transactions');

        $monzo = new Monzo(new Client(new Guzzle));

        $transactions = $monzo->as(static::$token)
            ->take(20)
            ->since('2016-01-01T00:00:00Z')
            ->transactions();

        $this->assertInstanceOf(Collection::class, $transactions);

        $this->assertCount(20, $transactions);

        $this->assertContainsOnly(Transaction::class, $transactions);
    }

    public function testSingleTransaction()
    {
        VCR::insertCassette('single_transaction');

        $monzo = new Monzo(new Client(new Guzzle));

        // grab a single transaction
        $transaction = $monzo->as(static::$token)
            ->take(1)
            ->since('2016-01-01T00:00:00Z')
            ->transactions(static::$account)
            ->first();

        // grab the ID

        $result = $monzo->as(static::$token)->transaction($transaction->id);

        $this->assertEquals($transaction->id, $result->id);
    }

    /**
     * @expectedException \Amelia\Monzo\Exceptions\AccessTokenExpired
     */
    public function testInvalidAccessTokens()
    {
        VCR::insertCassette('invalid_access_token');

        $monzo = new Monzo(new Client(new Guzzle));

        $monzo->as('bad-access-token')->accounts();
    }

    public function testFetchingBalance()
    {
        VCR::insertCassette('balance');

        $monzo = new Monzo(new Client(new Guzzle));

        $balance = $monzo->as(static::$token)->balance();

        $this->assertEquals(6394, $balance->balance);
        $this->assertEquals('GBP', $balance->currency);
    }

    public function testFetchingBalanceWithAccount()
    {
        VCR::insertCassette('balance');

        $monzo = new Monzo(new Client(new Guzzle));

        $balance = $monzo->as(static::$token)->balance(static::$account);

        $this->assertEquals(6394, $balance->balance);
        $this->assertEquals('GBP', $balance->currency);
    }

    public static function setUpBeforeClass()
    {
        // set these when adding new tests to get a real response
        static::$integrationToken = getenv('MONZO_TEST_ACCESS_TOKEN');
        static::$integrationAccount = getenv('MONZO_TEST_ACCOUNT');
        static::$integrationUser = getenv('MONZO_TEST_USER');

        static::$token = 'valid-access-token';
        static::$account = 'acc_test';
        static::$user = 'user_test';
    }
}
