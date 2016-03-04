<?php namespace SCT;

/**
 * Class Template
 * Super simple templating engine
 *
 * @package SCT
 */
class TemplateEngine
{
    private $vars = [];
    private $templatePath;

    public function __construct(string $templatePath)
    {
        $this->templatePath = realpath($templatePath);
    }

    public function __get(string $name)
    {
        return $this->vars[ $name ];
    }

    public function __set(string $name, string $value)
    {
        if ($name == 'view_template_file')
        {
            throw new \Exception("Cannot bind variable named 'view_template_file'");
        }

        $this->vars[ $name ] = $value;
    }

    /**
     * @param string $view_template_file The name of the template file
     * @param array $vars
     *
     * @return mixed
     * @throws \Exception
     */
    public function render(string $view_template_file, array $vars = [])
    {
        $this->vars = array_merge($this->vars, $vars);

        if (array_key_exists('view_template_file', $this->vars))
        {
            throw new \Exception("Cannot bind variable called 'view_template_file'");
        }

        extract($this->vars);
        ob_start();

        /** @noinspection PhpIncludeInspection */
        include($this->templatePath . "/$view_template_file");

        return ob_get_clean();
    }
}