<?php namespace Settings;

use SCT\SettingsInterface;

/**
 * Class AppSettings
 * Global app settings
 *
 * @package Settings
 */
class AppSettings implements SettingsInterface
{
    public static $base_url = 'sample.local';

    public static function init($environment) { }
}