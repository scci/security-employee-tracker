<?php
/**
 * Created by PhpStorm.
 * User: sdibble
 * Date: 12/7/2016
 * Time: 2:25 PM
 */

namespace SET\Handlers\DBConfigs;


/**
 * Class ConfigAbstract
 * @package SET\Handlers\DBConfigs
 */
abstract class ConfigAbstract
{
    /**
     * @var array
     */
    public $settings;

    public $allowable;

    /**
     *
     *
     * ConfigAbstract constructor.
     * @param array $settings
     */
    public function __construct($settings = [])
    {
        $this->settings = $settings;
    }

    public function implementAllowable()
    {
        foreach ($this->allowable as $configKey) {
            $this->setConfigValues($configKey);
        }
    }

    /**
     * All classes need to setup some kind of configuration data.
     * @return mixed
     */
    abstract public function setup();


    /**
     * First we set the value stored in the database. If there is no value, we go to what is in the config/env files.
     *
     * @param string $location
     * @param mixed $default
     */
    public function setConfigValues(string $location, $default = null)
    {
        $value = $default ?? $this->settings[$location] ?? config($location);
        config([$location => $value]);
    }
}