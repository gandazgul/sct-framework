<?php namespace SCT;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

/**
 * Class SessionHandler
 * To Implement session handling eventually, should be configurable
 *
 * @package SCT
 */
class SessionHandler extends NativeFileSessionHandler
{
    public function __construct()
    {
        parent::__construct(STORAGE . 'session/');
    }
}