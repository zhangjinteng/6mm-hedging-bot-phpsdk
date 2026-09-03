<?php

namespace Sixmm\HedgingBot\Exception;

use RuntimeException;

class RpcException extends RuntimeException
{
    private int $grpcCode;

    public function __construct(int $grpcCode, string $details)
    {
        $this->grpcCode = $grpcCode;
        parent::__construct($details !== '' ? $details : '6mm-hedging-bot gRPC request failed', $grpcCode);
    }

    public function grpcCode(): int
    {
        return $this->grpcCode;
    }
}
