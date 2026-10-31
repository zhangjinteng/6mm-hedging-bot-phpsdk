<?php

namespace Sixmm\HedgingBot\Contracts;

use Sixmm\HedgingBot\Proto\RunHedgeResponse;
use Sixmm\HedgingBot\Proto\EnqueueHedgeRunResponse;

/** Additive contract: legacy interface implementations remain source-compatible. */
interface ScopedHedgingBotClientInterface extends HedgingBotClientInterface
{
    public function runHedgeForOwner(string $ownerType, int $ownerId, int $configId, string $source = '', string $symbol = '', string $reason = ''): RunHedgeResponse;
    public function exitHedgeForOwner(string $ownerType, int $ownerId, int $configId, string $source = '', string $symbol = ''): RunHedgeResponse;
    public function enqueueHedgeRunForOwner(string $ownerType, int $ownerId, int $configId, string $source = '', string $symbol = '', string $reason = ''): EnqueueHedgeRunResponse;
}
