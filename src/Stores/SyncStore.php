<?php

namespace EthicalJobs\Quantify\Stores;

use Illuminate\Support\Collection;

/**
 * Sync report store
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class SyncStore implements Store
{
    /**
     * Current bucket key
     * 
     * @var string
     */
    protected $bucketKey = 'default';

    /**
     * Data storage buckets
     * 
     * @var Illuminate\Support\Collection
     */
    protected $buckets;

    /**
     * Object constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->buckets = new Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key) : array
    {
        return $this->getBucket()->get($key) ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, array $data) : array
    {
        $this->getBucket()->put($key, $data);

        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(string $key, array $data) : array
    {
        if ($metric = $this->get($key)) {

            $metric = array_merge($metric, $data);

            $this->set($key, $metric);
        }

        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key) : bool
    {
        return $this->getBucket()->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function all() : array
    {
        return $this->getBucket()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function flush() : void
    {
        $this->buckets->put($this->bucketKey, new Collection);
    }

    /**
     * {@inheritdoc}
     */
    public function setBucket(string $bucket) : Store
    {
        $this->bucketKey = $bucket;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBucket()
    {
        if ($this->buckets->has($this->bucketKey)) {
            return $this->buckets->get($this->bucketKey);
        }

        $this->buckets->put($this->bucketKey, new Collection);

        return $this->buckets->get($this->bucketKey);
    }
}
