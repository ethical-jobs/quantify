<?php

namespace App\Services\Reporting\Reporters;

use Notification;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;

/**
 * Distributed queue job measuring
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Queues implements Reporter
{
    /**
     * Store driver
     * 
     * @var EthicalJobs\Quantify\Stores\Store
     */
    protected $store;

    /**
     * Storage bucket key
     * 
     * @var string
     */
    protected static $bucket = 'queues';

    /**
     * Object constructor
     * 
     * @param Illuminate\Redis\RedisManager $redis
     */
    public function __construct(Store $store)
    {
        $this->store = $store;

        $this->store->setPrefix(static::getBucket());
    }

    /**
     * Track jobs on a queue
     *
     * @param Illuminate\Queue\Events\JobProcessed $event
     * @param int $numberOfJobs
     * @return void
     */
    public static function track(string $job, int $numberOfJobs) : array
    {
        $this->store->set($job, [
            'numberOfJobs'  => $numberOfJobs,
            'completed'     => 0,
            'average'       => 0,
            'total'         => 0,
            'i'             => microtime(true),
        ]);

        Queue::before(function (JobProcessing $event) use ($job) {
            return $this->beforeQueueJob($event, $job);
        });

        Queue::after(function (JobProcessed $event) use ($job) {
            return $this->afterQueueJob($event, $job);
        });
    }

    /**
     * Before queue job has finished callback
     *
     * @param Illuminate\Queue\Events\JobProcessed $event
     * @param string $job
     * @return void
     */
    protected function beforeQueueJob(JobProcessed $event, string $job) : void
    {
        if ($event->job->resolveName() !== $job) {
            return;
        }

        $this->store->update($job, [
            'i' => microtime(true),
        ]);        
    }

    /**
     * After queue job has finished callback
     *
     * @param Illuminate\Queue\Events\JobProcessed $event
     * @param string $job
     * @return void
     */
    protected function afterQueueJob(JobProcessed $event, string $job) : void
    {
        if ($event->job->resolveName() !== $job) {
            return;
        }

        $metric = $this->store->get($job);

        $executionTime = (microtime(true) - $metric['i']);

        $metric['total'] += $executionTime;

        $metric['completed']++;

        $average = $metric['total'] / $metric['completed'];

        $metric['average'] = $average;

        $this->store->set($job, $metric);

        if ($metric['completed'] === $metric['numberOfJobs']) {
            Trigger::notify();
        }        
    }

    /**
     * {@inheritdoc}
     */
    public function report() : array
    {
        return $this->store->all();
    }

    /**
     * {@inheritdoc}
     */
    public static function getBucket() : string
    {
        return 'queues';
    }          
}
