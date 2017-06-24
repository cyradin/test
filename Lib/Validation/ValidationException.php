<?php

namespace Lib\Validation;

class ValidationException extends \Exception
{
    /**
     * Used validation method
     * @var string
     */
    protected $method;

    public function __construct($method = '', $message = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }
}