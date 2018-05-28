<?php

namespace EthicalJobs\Quantify;

use Notification;
use EthicalJobs\Quantify\Stores\Store;

/**
 * Triggers a report notice
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Trigger
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
     * @return void
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Gather and report on buckets
     *
     * @return void
     */
    public function notify() : void
    {
        $reports = [];

        $buckets = new Buckets;

        foreach ($buckets as $bucket) {
            
            $this->store->setBucket($bucket);

            $reports[$bucket] = $this->store->all();
        }

        $this->send($reports);
    }

    /**
     * Dispatch report notification
     *
     * @param array $reports
     * @return void
     */
    protected function send(array $reports) : void
    {
        Notification::route('slack', config('quantify.channels.slack'))
            ->notify(new ReportNotice($reports)); 
    }   
}