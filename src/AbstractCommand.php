<?php

namespace Hystrix;

abstract class AbstractCommand
{
    protected $commandKey;

    protected $config;

    abstract protected function run();

    public function getCommandKey()
    {
        if ($this->commandKey) {
            return $this->commandKey;
        } else {
            return get_class($this);
        }
    }

    public function execute()
    {
        $circuitBreaker = $this->getCircuitBreaker();
        if (!$circuitBreaker->allowRequest()) {
            return $this->getFallbackOrThrowException();
        }
        try {
            $result = $this->run();
            $circuitBreaker->getMetrics()->markSuccess();
        }  catch (\Exception $exception) {
            $circuitBreaker->getMetrics()->markFailure();
            return $this->getFallback();
        }

        return $result;
    }

    private function getCircuitBreaker()
    {
        return $this->circuitBreaker;
    }

    private function getFallbackOrThrowException(\Exception $originalException = null)
    {
        return false;
    }

    protected function getFallback()
    {

    }
}
