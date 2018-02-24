<?php

namespace Hystrix;

class CommandMetrics
{
    const SUCCESS = 'SUCCESS';
    const FAILUR  = 'FAILURE';
    const IS_OPEN = 'IS_OPEN';

    private $commandKey;

    private $failure;

    private $successful;

    public $isCircuitOpen;

    private $storage;

    public function __construct($commandKey)
    {
        $this->commandKey = $commandKey;
        $this->storage = Storage::getInstance();
        $this->failure = $this->storage->get($this->commandKey . CommandMetrics::FAILUR);
        $this->successful = $this->storage->get($this->commandKey . CommandMetrics::SUCCESS);
        $this->isCircuitOpen = $this->storage->get($this->commandKey . CommandMetrics::IS_OPEN);
    }

    public function markSuccess()
    {
        $this->storage->incr($this->commandKey . CommandMetrics::SUCCESS);
    }

    public function markFailure()
    {
        $this->storage->incr($this->commandKey . CommandMetrics::FAILUR);
    }

    public function resetCounter()
    {
        $this->storage->del($this->commandKey . CommandMetrics::FAILUR);
        $this->storage->del($this->commandKey . CommandMetrics::SUCCESS);
    }

    public function getFailure()
    {
        return $this->failure;
    }

    public function getSuccessful()
    {
        return $this->successful;
    }

    public function getTotal()
    {
        return $this->successful + $this->failure;
    }

    public function getErrorPercentage()
    {
        $total = $this->getTotal();
        if (!$total) {
            return 0;
        } else {
            return $this->getFailure() / $total * 100;
        }
    }

    public function openCircuit($sleepWindowInSeconds)
    {
        $this->storage->setex($this->commandKey . CommandMetrics::IS_OPEN, $sleepWindowInSeconds, 1);
    }
}
