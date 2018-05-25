<?php

namespace Tests\Integration\Services;

use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;
use App\Services\Reporting\Reporter;
use App\Services\Reporting\Report;
use Tests\Fixtures\NullReportingQueueJob;

class ReporterTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_can_measure_a_metric()
    {
        Reporter::start('my-operation');
        sleep(2);
        Reporter::end('my-operation');

        Reporter::start('my-operation');
        sleep(1);
        Reporter::end('my-operation');

        $report = Reporter::report(false);

        $this->assertTrue($report['my-operation']['average'] > 2);
        $this->assertTrue($report['my-operation']['average'] < 4);

        $this->assertTrue($report['my-operation']['total'] > 2);
        $this->assertTrue($report['my-operation']['total'] < 4);

        $this->assertTrue(array_has($report['my-operation'], [
            'average', 'total', 'count'
        ]));
    }

    /**
     * @test
     * @group Integration
     */
    public function it_can_track_queue_jobs()
    {
        Notification::fake();

        Reporter::track(NullReportingQueueJob::class, 5);

        NullReportingQueueJob::dispatch();
        NullReportingQueueJob::dispatch();
        NullReportingQueueJob::dispatch();
        NullReportingQueueJob::dispatch();
        NullReportingQueueJob::dispatch();

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            Report::class,
            function ($notification, $channels) {
                dd($notification->toArray());
            }
        );
    }    
}
