<?php namespace SCT\Interfaces;

/**
 * Interface TemplateEngineInterface
 *
 * Interface for all template engines to implement
 *
 * @package Interfaces
 */
interface TemplateEngineInterface
{
    /**
     * TemplateEngineInterface constructor.
     *
     * @param string $templatePath The path where to find the template files, used by render to find the file
     */
    public function __construct(string $templatePath);

    /**
     * A getter for any variables bound to the template
     *
     * @param string $name The name of the variable
     *
     * @return mixed
     */
    public function __get(string $name);

    /**
     * A setter for any variables bound to the template
     *
     * @param string $name The name of the variable to set
     * @param string $value The value
     *
     * @return void
     *
     * @throws \SCT\Exceptions\TemplateEngineException when trying to set a variable called view_template_file
     */
    public function __set(string $name, string $value);

    /**
     * This function will take a template name and extra variables and returns a string with the rendered html
     *
     * @param string $view_template_file The name of the template file
     * @param array $vars An array of additional variables to bind to the template. Default is an empty array.
     *
     * @return string
     *
     * @throws \SCT\Exceptions\TemplateEngineException if one of the variables is called view_template_file
     */
    public function render(string $view_template_file, $vars = []);
}