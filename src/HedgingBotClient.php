<?php

namespace Sixmm\HedgingBot;

use Google\Protobuf\Struct;
use Grpc\ChannelCredentials;
use Sixmm\HedgingBot\Contracts\HedgingBotClientInterface;
use Sixmm\HedgingBot\Exception\RpcException;
use Sixmm\HedgingBot\Proto\CheckExchangeConnectionRequest;
use Sixmm\HedgingBot\Proto\CheckExchangeConnectionResponse;
use Sixmm\HedgingBot\Proto\EnqueueHedgeRunResponse;
use Sixmm\HedgingBot\Proto\ExchangeAccountConfig;
use Sixmm\HedgingBot\Proto\ExposureSnapshot;
use Sixmm\HedgingBot\Proto\HedgingServiceClient;
use Sixmm\HedgingBot\Proto\PositionSnapshot;
use Sixmm\HedgingBot\Proto\RunHedgeRequest;
use Sixmm\HedgingBot\Proto\RunHedgeResponse;
use Sixmm\HedgingBot\Proto\UpsertExposureRequest;
use Sixmm\HedgingBot\Proto\UpsertPositionRequest;
use UnexpectedValueException;

class HedgingBotClient implements \Sixmm\HedgingBot\Contracts\ScopedHedgingBotClientInterface
{
    private HedgingServiceClient $client;

    private int $timeoutMicroseconds;

    /** @var array<string, array<int, string>> */
    private array $metadata;

    public function __construct(string $target, int $timeoutMilliseconds = 10000, array $options = [], array $metadata = [])
    {
        if (trim($target) === '') {
            throw new \InvalidArgumentException('gRPC target cannot be empty');
        }
        if (!isset($options['credentials'])) {
            $options['credentials'] = ChannelCredentials::createInsecure();
        }
        $this->client = new HedgingServiceClient($target, $options);
        $this->timeoutMicroseconds = max(1, $timeoutMilliseconds) * 1000;
        $this->metadata = $metadata;
    }

    public function upsertExposure(UpsertExposureRequest $request): ExposureSnapshot
    {
        return $this->wait($this->client->UpsertExposure($request, $this->metadata, $this->callOptions()), ExposureSnapshot::class);
    }

    public function upsertPosition(UpsertPositionRequest $request): PositionSnapshot
    {
        return $this->wait($this->client->UpsertPosition($request, $this->metadata, $this->callOptions()), PositionSnapshot::class);
    }

    public function runHedge(int $configId, string $source = '', string $symbol = '', string $reason = ''): RunHedgeResponse
    {
        $request = $this->runRequest($configId, $source, $symbol)->setReason($reason);
        return $this->wait($this->client->RunHedge($request, $this->metadata, $this->callOptions()), RunHedgeResponse::class);
    }

    public function exitHedge(int $configId, string $source = '', string $symbol = ''): RunHedgeResponse
    {
        return $this->wait(
            $this->client->ExitHedge($this->runRequest($configId, $source, $symbol), $this->metadata, $this->callOptions()),
            RunHedgeResponse::class
        );
    }

    public function enqueueHedgeRun(int $configId, string $source = '', string $symbol = '', string $reason = ''): EnqueueHedgeRunResponse
    {
        $request = $this->runRequest($configId, $source, $symbol)->setReason($reason);
        return $this->wait(
            $this->client->EnqueueHedgeRun($request, $this->metadata, $this->callOptions()),
            EnqueueHedgeRunResponse::class
        );
    }

    public function checkExchangeConnection(array $account, array $assets = [], array $params = []): CheckExchangeConnectionResponse
    {
        $request = (new CheckExchangeConnectionRequest())
            ->setAccount($this->accountConfig($account))
            ->setAssets(array_values($assets));
        if ($params !== []) {
            $struct = new Struct();
            $struct->mergeFromJsonString(json_encode($params, JSON_THROW_ON_ERROR));
            $request->setParams($struct);
        }
        return $this->wait(
            $this->client->CheckExchangeConnection($request, $this->metadata, $this->callOptions()),
            CheckExchangeConnectionResponse::class
        );
    }

    private function runRequest(int $configId, string $source, string $symbol): RunHedgeRequest
    {
        if ($configId < 1 && $symbol === '') {
            throw new \InvalidArgumentException('config_id or symbol is required');
        }
        return (new RunHedgeRequest())->setConfigId($configId)->setSource($source)->setSymbol($symbol);
    }

    private function scopedRequest(string $type, int $id, int $configId, string $source, string $symbol): RunHedgeRequest
    {
        if (!in_array($type, ['merchant', 'tenant'], true) || $id < 1) throw new \InvalidArgumentException('Valid owner_type and positive owner_id are required');
        return $this->runRequest($configId, $source, $symbol)->setOwnerType($type)->setOwnerId($id);
    }

    public function runHedgeForOwner(string $ownerType, int $ownerId, int $configId, string $source = '', string $symbol = '', string $reason = ''): RunHedgeResponse
    {
        $request = $this->scopedRequest($ownerType, $ownerId, $configId, $source, $symbol)->setReason($reason);
        return $this->wait($this->client->RunHedge($request, $this->metadata, $this->callOptions()), RunHedgeResponse::class);
    }

    public function exitHedgeForOwner(string $ownerType, int $ownerId, int $configId, string $source = '', string $symbol = ''): RunHedgeResponse
    {
        return $this->wait($this->client->ExitHedge($this->scopedRequest($ownerType, $ownerId, $configId, $source, $symbol), $this->metadata, $this->callOptions()), RunHedgeResponse::class);
    }

    public function enqueueHedgeRunForOwner(string $ownerType, int $ownerId, int $configId, string $source = '', string $symbol = '', string $reason = ''): EnqueueHedgeRunResponse
    {
        $request = $this->scopedRequest($ownerType, $ownerId, $configId, $source, $symbol)->setReason($reason);
        return $this->wait($this->client->EnqueueHedgeRun($request, $this->metadata, $this->callOptions()), EnqueueHedgeRunResponse::class);
    }

    private function accountConfig(array $account): ExchangeAccountConfig
    {
        $message = new ExchangeAccountConfig();
        $stringSetters = [
            'exchange' => 'setExchange', 'account_name' => 'setAccountName', 'ccxt_id' => 'setCcxtId',
            'market_type' => 'setMarketType', 'default_settle' => 'setDefaultSettle',
            'account_type' => 'setAccountType', 'product_type' => 'setProductType', 'category' => 'setCategory',
            'position_mode' => 'setPositionMode', 'margin_mode' => 'setMarginMode', 'api_key' => 'setApiKey',
            'api_secret' => 'setApiSecret', 'passphrase' => 'setPassphrase',
        ];
        foreach ($stringSetters as $key => $setter) {
            if (array_key_exists($key, $account)) {
                $message->{$setter}((string) $account[$key]);
            }
        }
        $message->setSandbox((bool) ($account['sandbox'] ?? false));
        $message->setRecvWindowMs((int) ($account['recv_window_ms'] ?? 0));
        $message->setRateLimitMs((int) ($account['rate_limit_ms'] ?? 0));

        return $message;
    }

    private function callOptions(): array
    {
        return ['timeout' => $this->timeoutMicroseconds];
    }

    /** @template T */
    private function wait($call, string $expectedClass)
    {
        [$response, $status] = $call->wait();
        $code = (int) ($status->code ?? \Grpc\STATUS_UNKNOWN);
        if ($code !== \Grpc\STATUS_OK) {
            throw new RpcException($code, (string) ($status->details ?? ''));
        }
        if (!$response instanceof $expectedClass) {
            throw new UnexpectedValueException('Unexpected gRPC response type');
        }
        return $response;
    }
}
