<?php

namespace App\Services\Reporting\Reporters;

use Notification;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Queue;

/**
 * Distributed metrics measuring
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Metrics implements Reporter
{
    /**
     * Store driver
     * 
     * @var EthicalJobs\Quantify\Stores\Store
     */
    protected $store; 

    /**
     * Object constructor
     * 
     * @param Illuminate\Redis\RedisManager $redis
     */
    public function __construct(Store $store)
    {
        $this->store = $store;

        $this->store->setBucket(static::getBucket());
    }

    /**
     * Start measuring a metric cycle 
     *
     * @param string $key
     * @return array
     */
    public static function start(string $key) : array
    {
        if ($this->store->has($key)) {
            return $this->store->update($key, [
                'i' => microtime(true),
            ]);
        }

        return $this->store->set($key, [
            'count'     => 0,
            'total'     => 0,
            'average'   => 0,
            'i'         => microtime(true),
        ]);            
    }

    /**
     * Complete measuring a metric
     *
     * @param string $key
     * @return array
     */
    public static function complete(string $key) : array
    {
        $metric = $this->store->get($key);

        $executionTime = (microtime(true) - $metric['i']);

        $metric['total'] += $executionTime;

        $metric['count']++;

        $average = $metric['total'] / $metric['count'];

        $metric['average'] = $average;

        return $this->store->set($key, $metric);
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
        return 'metrics';
    }       
}
