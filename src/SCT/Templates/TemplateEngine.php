<?php namespace SCT\Templates;

use SCT\Interfaces\TemplateEngineInterface;

/**
 * Abstract Class TemplateEngine
 * A base for template engines
 *
 * @package SCT
 */
abstract class TemplateEngine implements TemplateEngineInterface
{
    protected $vars = [];
    protected $templatePath;

    /**
     * @inheritdoc
     */
    public function __construct(string $templatePath)
    {
        $this->templatePath = realpath($templatePath);
    }

    /**
     * @inheritdoc
     */
    public function __get(string $name)
    {
        return $this->vars[ $name ];
    }

    /**
     * @inheritdoc
     */
    public function __set(string $name, string $value)
    {
        if ($name == 'view_template_file')
        {
            throw new \Exception("Cannot bind variable named 'view_template_file'");
        }

        $this->vars[ $name ] = $value;
    }

    /**
     * @inheritdoc
     */
    abstract public function render(string $view_template_file, $vars = []);
}