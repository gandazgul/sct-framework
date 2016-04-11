<?php namespace SCT\Settings;

use SCT\Interfaces\SettingsInterface;

/**
 * Class Settings
 * Settings initializer
 *
 * @package Settings
 */
class Settings
{
    private static $settings_files = [];

    /**
     * Gets a list of php files from a directory
     *
     * @param string $directory Directory to list
     *
     * @return array List of paths
     */
    private static function get_php_file_paths($directory)
    {
        if (!static::$settings_files)
        {
            $files = [];
            if ($dir_list = scandir($directory))
            {
                foreach ($dir_list as $file_path)
                {
                    // skip dot directories
                    if ($file_path != '.' && $file_path != '..')
                    {
                        $full_path = $directory . '/' . $file_path;
                        if (substr($file_path, -4) == '.php')
                        {
                            $files[] = $full_path;
                        } elseif (is_dir($full_path))
                        {
                            $files = array_merge($files, self::get_php_file_paths($full_path));
                        }
                    }
                }
            }

            static::$settings_files = $files;
        }

        return static::$settings_files;
    }

    /**
     * Initializes settings for the selected environment
     *
     * @param $environment
     */
    public static function init($environment)
    {
        static::settings_map(function ($ns_path) use ($environment)
        {
            /** @var SettingsInterface $ns_path */
            $ns_path::init($environment);
        });
    }

    /**
     * Maps all settings files to a function
     *
     * @param callable $function
     */
    public static function settings_map(callable $function)
    {
        $base_dir = APPLICATION . "Settings";
        $settings_files = self::get_php_file_paths($base_dir);

        foreach ($settings_files as $file)
        {
            // $file is the full filesystem path
            // strip everything up to /$folder_name, and the .php extension
            $rel_path = substr($file, strlen(ROOT), -4);

            // convert / to \ so that it's a php namespace
            /** @var SettingsInterface $ns_path */
            $ns_path = str_replace("/", "\\", $rel_path);
            $oReflectionClass = new \ReflectionClass($ns_path);
            if (!$oReflectionClass->isAbstract() && $oReflectionClass->isSubclassOf(
                    '\\SCT\\Interfaces\\SettingsInterface'
                )
            )
            {
                $function($ns_path);
            }
        }
    }
}