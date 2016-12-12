<?php
/**
 * Created by PhpStorm.
 * User: sdibble
 * Date: 12/12/2016
 * Time: 12:11 PM
 */

namespace SET\Handlers;

use Carbon\Carbon;

trait DateFormat
{
    public function dateFormat($date)
    {
        // Already Y-m-d
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) return $date;

        //Convert Carbon to Y-m-d
        if ($date instanceof Carbon) return $date->format('Y-m-d');

        // sqlite Y-m-d H:i:s to Y-m-d
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
    }
}