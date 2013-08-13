<?php

use React\EventLoop\Factory;
use Clue\Promise\React\Timeout;
use React\Promise\Deferred;
use React\Promise\When;

require __DIR__ . '/../vendor/autoload.php';

class TimeoutTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->loop = Factory::create();
    }

    public function testTimesOutQuiteFast()
    {
        $timeout = new Timeout(new Deferred(), $this->loop, 0.001);

        // assert that this times out
        $timeout->then(null, function (Exception $e) { });

        $this->loop->run();
    }

    public function testAlreadyResolved()
    {
        $timeout = new Timeout(When::resolve(1), $this->loop, 1);

        $that = $this;
        $timeout->then(function ($value) use ($that) {
            $that->assertEquals(1, $value);
        });

        $this->loop->run();
    }
}
