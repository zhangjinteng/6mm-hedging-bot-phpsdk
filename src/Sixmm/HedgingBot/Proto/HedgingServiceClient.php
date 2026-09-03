<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Sixmm\HedgingBot\Proto;

/**
 */
class HedgingServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Sixmm\HedgingBot\Proto\UpsertExposureRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Sixmm\HedgingBot\Proto\ExposureSnapshot>
     */
    public function UpsertExposure(\Sixmm\HedgingBot\Proto\UpsertExposureRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/hedging.v1.HedgingService/UpsertExposure',
        $argument,
        ['\Sixmm\HedgingBot\Proto\ExposureSnapshot', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Sixmm\HedgingBot\Proto\UpsertPositionRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Sixmm\HedgingBot\Proto\PositionSnapshot>
     */
    public function UpsertPosition(\Sixmm\HedgingBot\Proto\UpsertPositionRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/hedging.v1.HedgingService/UpsertPosition',
        $argument,
        ['\Sixmm\HedgingBot\Proto\PositionSnapshot', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Sixmm\HedgingBot\Proto\RunHedgeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Sixmm\HedgingBot\Proto\RunHedgeResponse>
     */
    public function RunHedge(\Sixmm\HedgingBot\Proto\RunHedgeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/hedging.v1.HedgingService/RunHedge',
        $argument,
        ['\Sixmm\HedgingBot\Proto\RunHedgeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Sixmm\HedgingBot\Proto\RunHedgeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Sixmm\HedgingBot\Proto\RunHedgeResponse>
     */
    public function ExitHedge(\Sixmm\HedgingBot\Proto\RunHedgeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/hedging.v1.HedgingService/ExitHedge',
        $argument,
        ['\Sixmm\HedgingBot\Proto\RunHedgeResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Sixmm\HedgingBot\Proto\RunHedgeRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Sixmm\HedgingBot\Proto\EnqueueHedgeRunResponse>
     */
    public function EnqueueHedgeRun(\Sixmm\HedgingBot\Proto\RunHedgeRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/hedging.v1.HedgingService/EnqueueHedgeRun',
        $argument,
        ['\Sixmm\HedgingBot\Proto\EnqueueHedgeRunResponse', 'decode'],
        $metadata, $options);
    }

    /**
     * @param \Sixmm\HedgingBot\Proto\CheckExchangeConnectionRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall<\Sixmm\HedgingBot\Proto\CheckExchangeConnectionResponse>
     */
    public function CheckExchangeConnection(\Sixmm\HedgingBot\Proto\CheckExchangeConnectionRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/hedging.v1.HedgingService/CheckExchangeConnection',
        $argument,
        ['\Sixmm\HedgingBot\Proto\CheckExchangeConnectionResponse', 'decode'],
        $metadata, $options);
    }

}
