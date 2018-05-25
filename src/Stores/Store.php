<?php

namespace EthicalJobs\Quantify\Stores;

/**
 * State storage
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

interface Store
{
    /**
     * Get data
     *
     * @param string $key
     * @return array
     */
    public function get(string $key) : array;

    /**
     * Set data
     *
     * @param string $key
     * @param array $data
     * @return array
     */
    public function set(string $key, array $data) : array;

    /**
     * Merges values into a key
     *
     * @param string $key
     * @param array $data
     * @return array
     */
    public function update(string $key, array $data) : array;    

    /**
     * Checks if key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key) : bool;

    /**
     * Returns all items from the store
     *
     * @return array
     */
    public function all() : array;

    /**
     * Flushes all keys matching {prefix}
     *
     * @return void
     */
    public function flush() : void;    

    /**
     * Returns key prefix
     *
     * @return string
     */
    public function getBucket() : string;

    /**
     * Sets prefix key
     *
     * @param string $prefix
     * @return EthicalJobs\Quantify\Stores\Store
     */
    public function setBucket(string $prefix) : Store;
}
