<?php

namespace EthicalJobs\Quantify\Reporters;

/**
 * Gathers data for reporting
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

 interface Reporter
 {
     /**
      * Returns the report data
      *
      * @return array
      */
     public function report() : array;

    /**
     * Returns the reporters bucket name
     *
     * @return string
     */
    public static function getBucket() : string;     
 }