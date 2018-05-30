<?php

namespace EthicalJobs\Quantify\Reporters;

use EthicalJobs\Quantify\Stores\Store;

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
     * @param EthicalJobs\Quantify\Stores\Store $store
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
    public function start(string $key) : array
    {
        if ($this->store->has($key)) {
            return $this->store->update($key, [
                'i' => microtime(true),
            ]);
        }

        return $this->store->set($key, [
            'count'     => 0,
            'total-time'     => 0,
            'average-time'   => 0,
            'i'         => microtime(true),
        ]);            
    }

    /**
     * Complete measuring a metric
     *
     * @param string $key
     * @return array
     */
    public function complete(string $key) : array
    {
        $metric = $this->store->get($key);

        $executionTime = (microtime(true) - $metric['i']);

        $metric['total-time'] += $executionTime;

        $metric['count']++;

        $average = $metric['total-time'] / $metric['count'];

        $metric['average-time'] = $average;

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
