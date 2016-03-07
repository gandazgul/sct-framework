<?php namespace SCT\Settings;

use SCT\Interfaces\SettingsInterface;
use SCT\Interfaces\TemplateEngineInterface;

/**
 * Class AppSettings
 * Global app settings
 *
 * @package Settings
 */
class AppSettings implements SettingsInterface
{
    /** @var string The base url of this application */
    public static $base_url = 'sample.local';
    /** @var string Folder where views are kept */
    public static $view_folder = APPLICATION . 'views';
    /** @var TemplateEngineInterface A template engine class */
    public static $view_engine;

    public static function init($environment) { }
}