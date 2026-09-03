<?php

namespace Sixmm\HedgingBot\Contracts;

use Sixmm\HedgingBot\Proto\CheckExchangeConnectionResponse;
use Sixmm\HedgingBot\Proto\EnqueueHedgeRunResponse;
use Sixmm\HedgingBot\Proto\ExposureSnapshot;
use Sixmm\HedgingBot\Proto\PositionSnapshot;
use Sixmm\HedgingBot\Proto\RunHedgeResponse;
use Sixmm\HedgingBot\Proto\UpsertExposureRequest;
use Sixmm\HedgingBot\Proto\UpsertPositionRequest;

interface HedgingBotClientInterface
{
    public function upsertExposure(UpsertExposureRequest $request): ExposureSnapshot;

    public function upsertPosition(UpsertPositionRequest $request): PositionSnapshot;

    public function runHedge(int $configId, string $source = '', string $symbol = '', string $reason = ''): RunHedgeResponse;

    public function exitHedge(int $configId, string $source = '', string $symbol = ''): RunHedgeResponse;

    public function enqueueHedgeRun(int $configId, string $source = '', string $symbol = '', string $reason = ''): EnqueueHedgeRunResponse;

    public function checkExchangeConnection(array $account, array $assets = [], array $params = []): CheckExchangeConnectionResponse;
}
