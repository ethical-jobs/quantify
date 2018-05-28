<?php

namespace EthicalJobs\Quantify\Reporters;

use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;
use EthicalJobs\Quantify\Stores\Store;
use EthicalJobs\Quantify\Trigger;

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
     * Report notice trigger
     * 
     * @var EthicalJobs\Quantify\Trigger
     */
    protected $trigger;

    /**
     * Object constructor
     * 
     * @param EthicalJobs\Quantify\Stores\Store $store
     * @param EthicalJobs\Quantify\Trigger $trigger
     * @return void
     */
    public function __construct(Store $store, Trigger $trigger)
    {
        $this->store = $store;

        $this->store->setBucket(static::getBucket());

        $this->trigger = $trigger;
    }

    /**
     * Track jobs on a queue
     *
     * @param Illuminate\Queue\Events\JobProcessed $event
     * @param int $numberOfJobs
     * @return void
     */
    public function track(string $job, int $numberOfJobs) : void
    {
        $this->store->set($job, [
            'number-of-jobs'    => $numberOfJobs,
            'completed-jobs'    => 0,
            'average-time'      => 0,
            'total-time'        => 0,
            'i'                 => microtime(true),
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
     * @param Illuminate\Queue\Events\JobProcessing $event
     * @param string $job
     * @return void
     */
    protected function beforeQueueJob(JobProcessing $event, string $job) : void
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

        $metric['total-time'] += $executionTime;

        $metric['completed-jobs']++;

        $average = $metric['total-time'] / $metric['completed-jobs'];

        $metric['average-time'] = $average;

        unset($metric['i']);

        $this->store->set($job, $metric);

        if ($metric['completed-jobs'] === $metric['number-of-jobs']) {
            $this->trigger->notify();
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
