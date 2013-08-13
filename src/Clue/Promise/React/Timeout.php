<?php

namespace Clue\Promise\React;

use React\Promise\PromiseInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use RuntimeException;

class Timeout implements PromiseInterface
{
    private $deferred;
    private $loop;
    private $tid;

    public function __construct(PromiseInterface $promise, LoopInterface $loop, $timeout)
    {
        $this->loop = $loop;
        $this->tid = $loop->addTimer($timeout, array($this, 'timeout'));

        $this->promise = $promise;

        $this->deferred = new Deferred();

        $promise->then(array($this->deferred, 'resolve'), array($this->deferred, 'reject'));
        $promise->then(array($this, 'cancel'), array($this, 'cancel'));
    }

    public function then($fulfilledHandler = null, $errorHandler = null, $progressHandler = null)
    {
        return $this->deferred->then($fulfilledHandler, $errorHandler, $progressHandler);
    }

    public function timeout()
    {
        $this->cancel();

        // TODO: cancel base promise
        // $this->promise->cancel();

        $this->deferred->reject(new RuntimeException('Timeout'));
    }

    public function cancel()
    {
        if ($this->tid !== null) {
            $this->loop->cancelTimer($this->tid);
            $this->tid = null;
        }
    }
}