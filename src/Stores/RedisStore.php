<?php

namespace EthicalJobs\Quantify\Stores;

use Illuminate\Redis\RedisManager;

/**
 * Distributed report store
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class RedisStore implements Store
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
     * Store prefix
     * 
     * @var string
     */
    protected $prefix = '';       

    /**
     * Object constructor
     * 
     * @param Illuminate\Redis\RedisManager $redis
     */
    public function __construct(RedisManager $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key) : array
    {
        $prefixedKey = $this->getKey($key);

        $value = $this->redis->get($prefixedKey);

        return json_decode($value, true);
    }    

    /**
     * {@inheritdoc}
     */
    public function set(string $key, array $data) : array
    {
        $prefixedKey = $this->getKey($key);

        $this->redis->set($prefixedKey, json_encode($data));

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function update(string $key, array $data) : array
    {
        $current = $this->get($key);

        $merged = array_merge($current, $data);

        return $this->set($key, $merged);
    }    

    /**
     * {@inheritdoc}
     */
    public function has(string $key) : bool
    {
        $prefixedKey = $this->getKey($key);

        return $this->redis->exists($prefixedKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setBucket(string $prefix) : Store
    {
        $this->prefix = static::$namespace . $prefix;

        return $this;
    }    

    /**
     * {@inheritdoc}
     */
    public function getBucket() : string
    {
        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function all() : array
    {
        $rawKeys = $this->redis->keys($this->prefix() . '*');

        foreach ($rawKeys as $rawKey) {

            $withoutPrefix = str_after($rawKey, static::$prefix);

            $setKey = str_before($withoutPrefix, ':');

            $itemKey = str_after($withoutPrefix, ':');

            if ($setKey === $itemKey) {
                $allKeydData[$setKey] = $this->redis->get($rawKey);
            } else {
                $allKeydData[$setKey][$itemKey] = $this->redis->get($rawKey);
            }
        }

        return empty($allKeydData) ? [] : $allKeydData;        
    }

    /**
     * {@inheritdoc}
     */
    public function flush() : void
    {
        $keys = $this->redis->keys(static::$prefix . '*');

        foreach ($keys as $key) {
            $this->redis->del($key);
        }        
    }

    /**
     * Returns a key with prefix
     *
     * @param string $key
     * @return string
     */
    protected function getKey(string $key) : string
    {
        return $this->getBucket() . $key;
    }       
}
