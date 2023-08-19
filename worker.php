<?php

use Spiral\RoadRunner;
use Nyholm\Psr7;

include __DIR__ . "/vendor/autoload.php";

$worker = RoadRunner\Worker::create();
$psrFactory = new Psr7\Factory\Psr17Factory();

$worker = new RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

while ($req = $worker->waitRequest()) {
    try {
        $rsp = new Psr7\Response();
        ob_start();
        require __DIR__ . "/index.php";
        $data = ob_get_clean();
        $rsp->getBody()->write($data);

        $worker->respond($rsp);
    } catch (\Throwable $e) {
        $worker->getWorker()->error((string)$e);
    }
}