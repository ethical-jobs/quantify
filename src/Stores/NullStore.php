<?php

namespace EthicalJobs\Quantify\Stores;

/**
 * Null report store
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class NullStore implements Store
{
    /**
     * Redis storage driver
     * 
     * @var Illuminate\Redis\RedisManager
     */
    protected $redis;

    /**
     * Storage namespace
     * 
     * @var string
     */
    protected static $namespace = 'ej:quantify:';

    /**
     * Store bucket
     * 
     * @var string
     */
    protected $bucket = '';

    /**
     * {@inheritdoc}
     */
    public function get(string $key) : array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, array $data) : array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function update(string $key, array $data) : array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key) : bool
    {
        false;
    }

    /**
     * {@inheritdoc}
     */
    public function all() : array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function flush() : void
    {
        // Null
    }

    /**
     * {@inheritdoc}
     */
    public function setBucket(string $bucket) : Store
    {
        $this->bucket = $bucket;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBucket() : string
    {
        return $this->bucket ? $this->bucket . ':' : '';
    }

    /**
     * Returns a key with prefix
     *
     * @param string $key
     * @return string
     */
    protected function getKey(string $key) : string
    {
        return static::$namespace . $this->getBucket() . $key;
    }
}
