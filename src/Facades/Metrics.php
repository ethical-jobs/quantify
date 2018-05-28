<?php

namespace EthicalJobs\Quantify\Facades;

use Illuminate\Support\Facades\Facade;
use EthicalJobs\Quantify\Reporters\Metrics as MetricsService;

/**
 * Laravel facade accessor
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Metrics extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MetricsService::class;
    }
}