<?php namespace TestApplication\Settings;

/**
 * Class AppSettings
 * Global app settings
 *
 * @package Settings
 */
class AppSettings extends \SCT\Settings\AppSettings
{
    public static function init($environment)
    {
        parent::init($environment);

        switch ($environment)
        {
            case 'local':
                static::$view_engine = '\\SCT\\Templates\\SmartyTemplateEngine';
                break;
        }
    }
}