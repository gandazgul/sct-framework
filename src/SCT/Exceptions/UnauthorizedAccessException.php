<?php namespace SCT\Exceptions;

/**
 * Class UnauthorizedAccessException
 *
 * Used in general to stop the request if access is not allowed
 *
 * @package SCT\Exceptions
 */
class UnauthorizedAccessException extends SCTException
{
    /**
     * UnauthorizedAccessException constructor.
     */
    public function __construct() { }
}