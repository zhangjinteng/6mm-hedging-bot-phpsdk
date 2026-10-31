<?php
// php tests/owner_scope.php /path/to/vendor/autoload.php; serialization only, no channels/RPC.
require $argv[1] ?? dirname(__DIR__).'/vendor/autoload.php';

use Sixmm\HedgingBot\HedgingBotClient;
use Sixmm\HedgingBot\Contracts\ScopedHedgingBotClientInterface;
use Sixmm\HedgingBot\Proto\RunHedgeRequest;
use Sixmm\HedgingBot\Proto\UpsertExposureRequest;
use Sixmm\HedgingBot\Proto\UpsertPositionRequest;

$checks=0;
$check=static function(bool $ok) use (&$checks): void {++$checks;if(!$ok)throw new RuntimeException('Check failed: '.$checks);};
foreach ([RunHedgeRequest::class,UpsertExposureRequest::class,UpsertPositionRequest::class] as $class) {
    $message=(new $class())->setOwnerType('tenant')->setOwnerId(900003);
    $copy=new $class();$copy->mergeFromString($message->serializeToString());
    $check($copy->getOwnerType()==='tenant');$check((int)$copy->getOwnerId()===900003);
    if (method_exists($copy,'getAgentId')) $check((int)$copy->getAgentId()===0);
}
$reflection=new ReflectionClass(HedgingBotClient::class);$client=$reflection->newInstanceWithoutConstructor();
$check($client instanceof ScopedHedgingBotClientInterface);
$method=$reflection->getMethod('scopedRequest');$method->setAccessible(true);
$request=$method->invoke($client,'tenant',101,18,'','');
$check((int)$request->getConfigId()===18);$check($request->getOwnerType()==='tenant');$check((int)$request->getOwnerId()===101);
foreach ([['tenant',0],['',1],['platform',1]] as [$type,$id]) {
    try {$method->invoke($client,$type,$id,18,'','');throw new RuntimeException('Invalid owner accepted');}
    catch (InvalidArgumentException $e) {++$checks;}
}
echo "Owner SDK checks passed: {$checks}\n";
