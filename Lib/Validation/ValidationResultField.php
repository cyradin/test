<?php

namespace Lib\Validation;

class ValidationResultField
{
    /**
     * Field code
     * @var string
     */
    protected $code;

    /**
     * Field value
     * @var mixed
     */
    protected $value;

    /**
     * Field raw value
     * @var mixed
     */
    protected $rawValue;

    /**
     * Validation errors
     * @var array
     */
    protected $errors;

    public function __construct(string $code, $value, $rawValue, array $errors = [])
    {
        $this->code     = $code;
        $this->value    = $value;
        $this->rawValue = $rawValue;
        $this->errors   = $errors;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRawValue()
    {
        return $this->rawValue;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

}
