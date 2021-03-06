<?php

namespace Tests\Integration\Reporters;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Event;
use Illuminate\Notifications\AnonymousNotifiable;
use EthicalJobs\Quantify\Reporters\Queues;
use EthicalJobs\Quantify\ReportNotice;
use Tests\Fixtures;

class ReporterTest extends \Tests\TestCase
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
    public function it_has_correct_bucket_name()
    {
        $this->assertTrue(
            Queues::getBucket() === 'queues'
        );
    }

    /**
     * @test
     * @group Integration
     */
    public function it_does_not_send_notifications_before_all_jobs_complete()
    {
        Notification::fake();

        $reporter = resolve(Queues::class);

        $reporter->track(Fixtures\UsleepQueueJob::class, 40);

        for ($i = 0; $i < 31; $i++) {
            Fixtures\UsleepQueueJob::dispatch();
        }

        Notification::assertNotSentTo(new AnonymousNotifiable(), ReportNotice::class);
    }    

    /**
     * @test
     * @group Integration
     */
    public function it_sends_notifications_when_all_jobs_complete()
    {
        Notification::fake();

        $queueReporter = resolve(Queues::class);

        $queueReporter->track(Fixtures\UsleepQueueJob::class, 30);

        for ($i = 0; $i < 31; $i++) {
            Fixtures\UsleepQueueJob::dispatch();
        }

        Notification::assertSentTo(new AnonymousNotifiable(), ReportNotice::class);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_wont_track_unspecified_jobs()
    {
        Notification::fake();

        $reporter = resolve(Queues::class);

        $reporter->track(Fixtures\UsleepQueueJob::class, 3);

        Fixtures\UsleepQueueJob::dispatch();

        Fixtures\SleepQueueJob::dispatch();
        Fixtures\SleepQueueJob::dispatch();

        Notification::assertNotSentTo(new AnonymousNotifiable(), ReportNotice::class);
    }

    /**
     * @test
     * @group Integration
     */
    public function it_knows_if_a_job_is_bing_tracked()
    {
        $queueReporter = resolve(Queues::class);

        $queueReporter->track(Fixtures\UsleepQueueJob::class, 5);

        $this->assertTrue($queueReporter->isJobTracked(Fixtures\UsleepQueueJob::class));

        $this->assertFalse($queueReporter->isJobTracked(Fixtures\SleepQueueJob::class));
    }    

    /**
     * @test
     * @group Integration
     */
    public function it_reports_correct_metrics()
    {
        Notification::fake();

        $reporter = resolve(Queues::class);

        $reporter->track(Fixtures\UsleepQueueJob::class, 3);

        Fixtures\UsleepQueueJob::dispatch();
        Fixtures\UsleepQueueJob::dispatch();
        Fixtures\UsleepQueueJob::dispatch();

        Notification::assertSentTo(new AnonymousNotifiable(), ReportNotice::class, function ($notification, $channels, $notifiable) {
            $report = $notification->toArray();
            return array_has($report['queues'][0], [
                'job', 'number-of-jobs', 'completed-jobs', 'failed-jobs', 'average-time', 'total-time',
            ]);
        });
    }        

    /**
     * @test
     * @group Integration
     */
    public function it_reports_on_the_number_of_jobs()
    {
        Notification::fake();

        $reporter = resolve(Queues::class);

        $reporter->track(Fixtures\UsleepQueueJob::class, 3);

        Fixtures\UsleepQueueJob::dispatch();
        Fixtures\UsleepQueueJob::dispatch();
        Fixtures\UsleepQueueJob::dispatch();

        Notification::assertSentTo(new AnonymousNotifiable(), ReportNotice::class, function ($notification, $channels, $notifiable) {
            $report = $notification->toArray();
            return $report['queues'][0]['number-of-jobs'] === 3 
                && $report['queues'][0]['completed-jobs'] === 3;
        });
    }

    /**
     * @test
     * @group Integration
     */
    public function it_reports_on_the_average_job_run_time()
    {
        Notification::fake();

        $reporter = resolve(Queues::class);

        $reporter->track(Fixtures\UsleepQueueJob::class, 3);

        Fixtures\UsleepQueueJob::dispatch();
        Fixtures\UsleepQueueJob::dispatch();
        Fixtures\UsleepQueueJob::dispatch();

        Notification::assertSentTo(new AnonymousNotifiable(), ReportNotice::class, function ($notification, $channels, $notifiable) {
            $report = $notification->toArray();
            return is_float($report['queues'][0]['average-time']);
        });
    }     
}
