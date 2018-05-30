<?php

namespace EthicalJobs\Quantify\Reporters;

use Illuminate\Queue;
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
            'job' => $job,
            'number-of-jobs' => $numberOfJobs,
            'completed-jobs' => 0,
            'failed-jobs' => 0,
            'average-time' => 0,
            'total-time' => 0,
            'i' => microtime(true),
        ]);
    }

    /**
     * Is a queue job being tracked
     *
     * @return bool
     */
    public function isJobTracked(string $job) : bool
    {
        $report = collect($this->report());

        $tracked = $report->pluck('job');

        return $tracked->contains($job);
    }    

    /**
     * Before queue jobs are finished callback
     *
     * @param Illuminate\Queue\Events\JobProcessing $event
     * @return void
     */
    public function startQueueJob(Queue\Events\JobProcessing $event) : void
    {
        $job = $event->job->resolveName();
        
        if ($this->isJobTracked($job) === false) {
            return;
        }

        $this->store->update($job, [
            'i' => microtime(true),
        ]);
    }

    /**
     * Completed / Failed queue jobs callback
     *
     * @param mixed $event
     * @return void
     */
    public function completeQueueJob($event) : void
    {
        $job = $event->job->resolveName();

        if ($this->isJobTracked($job) === false) {
            return;
        }

        $metric = $this->store->get($job);

        switch (get_class($event)) {
            case Queue\Events\JobProcessed::class:
                $metric['completed-jobs']++;
                break;
            case Queue\Events\JobFailed::class:
                $metric['failed-jobs']++;
                break;
            case Queue\Events\JobExceptionOccurred::class:
                $metric['failed-jobs']++;
                break;                                  
        }   

        $processedJobs = $metric['completed-jobs'] + $metric['failed-jobs'];

        $metric['total-time'] += (microtime(true) - $metric['i']);

        $metric['average-time'] = $metric['total-time'] / ($processedJobs);

        $this->store->set($job, $metric);        

        if ($processedJobs === $metric['number-of-jobs']) {
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
