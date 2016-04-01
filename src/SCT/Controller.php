<?php namespace SCT;

/**
 * Class Controller
 *
 * @package SCT
 *
 * Base controller
 */
class Controller
{
    protected $template = null;

    public function getTemplate()
    {
        return $this->template;
    }

    public function getDefaultData()
    {
        return [];
    }


}