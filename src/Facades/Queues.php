<?php

namespace EthicalJobs\Quantify\Facades;

use Illuminate\Support\Facades\Facade;
use EthicalJobs\Quantify\Reporters\Queues as QueuesService;

/**
 * Laravel facade accessor
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Queues extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return QueuesService::class;
    }
}