<?php

namespace EthicalJobs\Quantify;

/**
 * Array helper function
 * 
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class Arr
{
    /**
     * Removes all occurences of {needle} in {subject}
     *
     * @return array
     */
    public static function purgeKeys(string $needle, array $subject) : array
    {
        foreach ($subject as $key => $value) {
            if (is_array($value)) {
                $subject[$key] = static::purgeKeys($needle, $value);
            } else if ($key === $needle) {
                unset($subject[$needle]);
            }
        }

        return $subject;
    }
}
