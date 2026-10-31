# 6mm-hedging-bot PHP SDK

PHP gRPC client for `6mm-hedging-bot`.

## Requirements

- PHP 8.1+
- `ext-grpc`
- `google/protobuf`

## Installation

```bash
composer require zhangjinteng/6mm-hedging-bot-phpsdk
```

Before the first tagged release is available, register the Git repository and
install `dev-main`:

```bash
composer config repositories.6mm-hedging-bot-phpsdk vcs https://github.com/zhangjinteng/6mm-hedging-bot-phpsdk.git
composer require zhangjinteng/6mm-hedging-bot-phpsdk:dev-main
```

## Usage

```php
use Sixmm\HedgingBot\HedgingBotClient;

$client = new HedgingBotClient('127.0.0.1:7893', 10000);
$result = $client->runHedge(18);

echo $result->getDecision()->getAction();
```

Connection checks accept the same exchange account fields as the Go adapter:

```php
$response = $client->checkExchangeConnection([
    'exchange' => 'Bitget',
    'account_name' => 'Bitget demo',
    'ccxt_id' => 'bitget',
    'market_type' => 'swap',
    'sandbox' => true,
    'default_settle' => 'USDT',
    'account_type' => 'classic',
    'product_type' => 'USDT-FUTURES',
    'position_mode' => 'hedge',
    'margin_mode' => 'cross',
    'api_key' => '...',
    'api_secret' => '...',
    'passphrase' => '...',
], ['USDT']);
```

The checked-in generated classes are built from `proto/hedging/v1/hedging.proto`.
After updating the contract, run `composer generate` with `protoc` and
`grpc_php_plugin` installed.

## Merchant and white-label ownership

The updated server accepts `owner_type` (`merchant` or `tenant`) and positive
`owner_id` on exposure/position requests and `RunHedgeRequest`. Owner identity
must be derived by a trusted backend from its authenticated session, not posted
by an end user. A tenant ID is never an `agent_id`: tenant requests keep
`agent_id=0`; merchant requests retain the existing merchant ID.

```php
// $tenantId comes from the authenticated white-label account.
$result = $client->runHedgeForOwner('tenant', $tenantId, $configId);
$closing = $client->exitHedgeForOwner('tenant', $tenantId, $configId);
$queued = $client->enqueueHedgeRunForOwner('tenant', $tenantId, $configId);

$exposure = (new \Sixmm\HedgingBot\Proto\UpsertExposureRequest())
    ->setOwnerType('tenant')->setOwnerId($tenantId)->setAgentId(0)
    ->setSymbol('BTCUSDT')->setMarkPrice('100')->setNetQuantity('1');
```

`ScopedHedgingBotClientInterface` is additive: the legacy interface and method
signatures are unchanged. Existing config-ID-only calls work for merchants;
tenant calls require explicit ownership. Symbol-only resolution requires an
owner and rejects ambiguous configurations. Deploy migration 000020 and the
updated Go server before enabling tenant calls. RPC ownership fields are not
authentication: keep the robot's HTTP/gRPC ports private to trusted backends.

Tenant automatic exposure aggregates only current direct users in enabled
sites belonging to that tenant (`users.agent_id=0 OR users.agent_id IS NULL`).
The robot requires `FREEDEX_DB` for the authoritative PostgreSQL directory.
Scope lookup failures fail closed; there is no fallback to all zero-agent users.
