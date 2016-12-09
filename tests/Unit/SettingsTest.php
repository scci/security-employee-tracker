<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Setting;

class SettingsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_returns_a_value_based_on_a_given_key()
    {
        $setting = factory(Setting::class)->create();

        $returned = Setting::get($setting->key);

        $this->assertEquals($setting->value, $returned);
    }

    /** @test */
    public function it_returns_the_default_if_no_value_is_found()
    {
        $returned = Setting::get('Nonexistant.Key', 'Some Default Value');

        $this->assertEquals($returned, 'Some Default Value');
    }

    /** @test */
    public function it_stores_data_in_the_cache()
    {
        $setting = factory(Setting::class)->create();

        $this->assertEquals($setting->value, Cache::get($setting->key));
    }

    /**
     * @test
     * This tests the AppServiceProvider where we hook into the updating setting.
     */
    public function it_updates_the_cache_when_settings_are_updated()
    {
        $setting = factory(Setting::class)->create();
        Setting::updateOrCreate(['key' => $setting->key], ['value' => 'New Value']);

        $this->assertEquals('New Value', Cache::get($setting->key));
    }

    /** @test */
    public function it_gets_all_records_as_an_array()
    {
        $settings = factory(Setting::class, 3)->create();
        $returned = Setting::getAll();

        //Filter out any pre-existing settings.
        foreach ($returned as $key => $value) {
            if (!in_array($key, $settings->pluck('key')->toArray())) {
                unset($returned[$key]);
            }
        }

        $this->assertCount(3, $returned);
    }

    /** @test */
    public function it_sets_new_setting_values()
    {
        Setting::set('someKey', 'someValue');
        $this->assertEquals('someValue', Setting::get('someKey'));
    }

    /** @test */
    public function it_updates_an_existing_setting()
    {
        $setting = factory(Setting::class)->create();
        Setting::set($setting->key, 'newValue');

        $this->assertEquals('newValue', Setting::get($setting->key));
    }
}
