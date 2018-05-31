<?php

namespace Tests\Integration;

use Illuminate\Queue;
use EthicalJobs\Quantify\Stores;
use Illuminate\Support\Facades\Event;

class TestingSwitchTest extends \Tests\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        config([
            'quantify.testing-disabled' => true,
        ]);
    }	

    /**
     * @test
     * @group Integration
     */
    public function it_binds_null_store_when_testing_enabled()
    {
        $store = resolve(Stores\Store::class);

        $this->assertInstanceOf(Stores\NullStore::class, $store);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_does_not_listen_to_queus_when_testing_enabled()
    {
        $this->assertFalse(Event::hasListeners(Queue\Events\JobProcessing::class));

        $this->assertFalse(Event::hasListeners(Queue\Events\JobProcessed::class));

        $this->assertFalse(Event::hasListeners(Queue\Events\JobFailed::class));

        $this->assertFalse(Event::hasListeners(Queue\Events\JobExceptionOccurred::class));
    }    
}
