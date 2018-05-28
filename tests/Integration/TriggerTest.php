<?php

namespace Tests\Integration;

use Mockery;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use EthicalJobs\Quantify\Stores\Store;
use EthicalJobs\Quantify\ReportNotice;
use EthicalJobs\Quantify\Buckets;
use EthicalJobs\Quantify\Trigger;

class TriggerTest extends \Tests\TestCase
{
    /**
     * Post test execution
     *
     * @return void
     */
    public function tearDown()
    {
        Redis::FLUSHALL();
    }

    /**
     * @test
     * @group Integration
     */
    public function it_reports_on_all_buckets()
    {
        Notification::fake();

        $buckets = new Buckets;

        $store = Mockery::mock(Store::class);

        foreach ($buckets->all() as $bucket) {
            $store
                ->shouldReceive('setBucket')
                ->with($bucket)
                ->once()
                ->shouldReceive('all')
                ->withNoArgs()
                ->andReturn(["bucked-$bucket"]);
        }       

        $trigger = new Trigger($store);

        $trigger->notify();

        Notification::assertSentTo(new AnonymousNotifiable(), ReportNotice::class, 
            function ($notification, $channels, $notifiable) use ($buckets) {
                return array_has(
                    $notification->toArray(), 
                    $buckets->keys()->toArray()
                );
            }
        );
    }
}
