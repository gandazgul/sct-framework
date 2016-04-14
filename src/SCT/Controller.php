<?php namespace SCT;

use Symfony\Component\HttpFoundation\Request;

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

    /**
     * Check for security like session or API tokens, etc
     *
     * @param Request $request
     *
     * @return bool
     */
    public function shouldRequestContinue(Request $request)
    {
        return true;
    }
}