<?php

namespace EthicalJobs\Quantify;

use Notification;

/**
 * Triggers a report notice
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Trigger
{
    /**
     * Gather and report on buckets
     *
     * @return void
     */
    public static function notify() : void
    {
        $reports = [];

        $buckets = new Buckets;

        foreach ($buckets as $bucket) {

            $this->store->setPrefix($bucket);

            $reports[$bucket] = $this->store->all();
        }

        static::send($reports);
    }

    /**
     * Dispatch report notification
     *
     * @param array $reports
     * @return void
     */
    protected static function send(array $reports) : void
    {
        Notification::route('slack', config('quantify.channels.slack'))
            ->notify(new ReportNotice($reports)); 
    }   
}