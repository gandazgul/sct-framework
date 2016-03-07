<?php namespace SCT\Templates;

use SCT\Exceptions\TemplateEngineException;

/**
 * Class PHPTemplateEngine
 * Super simple templating engine for php files
 *
 * @package SCT
 */
class PHPTemplateEngine extends TemplateEngine
{
    /**
     * Renders a PHP file as a view by using output buffering
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

        extract($this->vars);
        ob_start();

        /** @noinspection PhpIncludeInspection */
        include($this->templatePath . "/$view_template_file");

        return ob_get_clean();
    }
}