<?php namespace SCT\Settings;

/**
 * Class AppSettings
 * Global app settings
 *
 * @package Settings
 */
class AppSettings implements SettingsInterface
{
    public static $base_url = 'sample.local';
    public static $view_folder = APPLICATION . '/views';

    public static function init($environment) { }
}