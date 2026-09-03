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
