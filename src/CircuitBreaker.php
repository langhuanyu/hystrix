<?php

namespace Hystrix;

class CircuitBreaker
{
    private $metrics;

    private $commandKey;

    private $config;

    public function __construct($commandKey, $commandConfig)
    {
        $this->commandKey = $commandKey;
        $this->metrics = new CommandMetrics($commandKey);
        $this->config = $commandConfig;
    }

    public function isOpen()
    {
        if ($this->metrics->isCircuitOpen) {
            return true;
        }

        if ($this->metrics->getTotal() < $this->config['circuitBreaker']['requestVolumeThreshold']) {
            return false;
        }

        $allowedErrorPercentage = $this->config['circuitBreaker']['errorThresholdPercentage'];
        if ($this->metrics->getErrorPercentage() < $allowedErrorPercentage) {
            return false;
        } else {
            $this->metrics->openCircuit($this->config['circuitBreaker']['sleepWindowInSeconds']);
            $this->metrics->resetCounter();
            return true;
        }
    }

    public function allowRequest()
    {
        if ($this->config['circuitBreaker']['forceOpen']) {
            return false;
        }
        if ($this->config['circuitBreaker']['forceClosed']) {
            return true;
        }

        return !$this->isOpen();
    }

    public function getMetrics()
    {
        return $this->metrics;
    }
}
