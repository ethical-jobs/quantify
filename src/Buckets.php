<?php

namespace EthicalJobs\Quantify;

use EthicalJobs\Quantify\Reporters;

/**
 * Collection of report buckets
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Buckets extends \Illuminate\Support\Collection
{
    /**
     * Create a new collection.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct([
            Reporters\Metrics::getBucket(),
            Reporters\Queues::getBucket(),
        ]);
    }    
}
