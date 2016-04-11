<?php namespace SCT\Templates;

use SCT\Exceptions\TemplateEngineException;
use SCT\Settings\Settings;

/**
 * Class PHPTemplateEngine
 * Super simple templating engine for smarty files
 *
 * @package SCT
 */
class SmartyTemplateEngine extends TemplateEngine
{
    protected $smarty;

    public function __construct(string $templatePath)
    {
        parent::__construct($templatePath);

        $this->smarty = new \Smarty();

        $this->smarty->setTemplateDir($templatePath);
        $this->smarty->setCompileDir(APPLICATION . 'storage/templates_c/');
        $this->smarty->setConfigDir(APPLICATION . 'storage/configs/');
        $this->smarty->setCacheDir(APPLICATION . 'storage/cache/');
        $this->smarty->setCaching(true);
        $this->smarty->auto_literal = false;

        Settings::settings_map(function ($ns_path)
        {
            $this->smarty->registerClass(class_basename($ns_path), $ns_path);
        });
    }

    /**
     * Renders a .smarty file using Smarty
     *
     * @param string $view_template_file
     * @param array $vars
     *
     * @return mixed
     *
     * @throws TemplateEngineException
     */
    public function render(string $view_template_file, $vars = [])
    {
        if ($vars)
        {
            $this->vars = array_merge($this->vars, $vars);
        }

        if (array_key_exists('view_template_file', $this->vars))
        {
            throw new TemplateEngineException("Cannot bind variable called 'view_template_file'");
        }

        $this->smarty->assign($this->vars);

        return $this->smarty->fetch($view_template_file);
    }
}