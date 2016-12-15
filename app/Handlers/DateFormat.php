<?php
/**
 * Created by PhpStorm.
 * User: sdibble
 * Date: 12/12/2016
 * Time: 12:11 PM.
 */

namespace SET\Handlers;

use Carbon\Carbon;

trait DateFormat
{
    public function dateFormat($date)
    {
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
            return $date;
        }

        if ($date instanceof Carbon or $date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }

        // sqlite
        if ($carbonDate = Carbon::createFromFormat('Y-m-d H:i:s', $date)) {
            return $carbonDate->format('Y-m-d');
        }

        // JPAS
        if ($carbonDate = \DateTime::createFromFormat('n/j/Y G:i', $date)) {
            return $carbonDate->format('Y-m-d');
        }

        return null;
    }
}
