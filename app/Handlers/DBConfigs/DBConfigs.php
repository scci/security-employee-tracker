<?php
/**
 * Created by PhpStorm.
 * User: sdibble
 * Date: 12/7/2016
 * Time: 2:17 PM
 */

namespace SET\Handlers\DBConfigs;

use Illuminate\Support\Facades\Schema;
use SET\Setting;

/**
 * Class DBConfigs
 * @package SET\Handlers\DBConfigs
 */
class DBConfigs
{

    /**
     * @var array
     */
    protected static $classes = [
        Ldap::class,
        Mail::class,
    ];

    /**
     *  Cycle through our classes and setup configuration data for each.
     */
    public static function execute()
    {
        if (!Schema::hasTable('settings')) return;
        $settings = Setting::getAll();
        foreach (static::$classes as $class)
        {
            (new $class($settings))->setup();
        }
    }
}