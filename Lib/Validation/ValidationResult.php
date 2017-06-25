<?php

namespace Lib\Validation;

class ValidationResult implements \Iterator
{
    /**
     *
     * @var [ValidationResultField]
     */
    protected $data;

    /**
     * All errors
     * @var array
     */
    protected $errors;

    /**
     * Current iterator position
     * @var int
     */
    protected $position;

    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * Move pointer to the first element
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Returns current element
     * @return ValidationResultField
     */
    public function current(): ?ValidationResultField
    {
        return $this->data[$this->position];
    }

    /**
     * Get current pointer
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Move pointer to the next element
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Check if element exists
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    /**
     * Add one more element
     * @param ValidationResultField $field
     */
    public function addField(ValidationResultField $field): void
    {
        if ($errors = $field->getErrors()) {
            $this->errors[$field->getCode()] = $errors;
        }

        $this->data[] = $field;
    }

    /**
     * Check if there are no errors
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return all field codes and values
     * @return array [code => value]
     */
    public function getValues(): ?array
    {
        $result = [];
        foreach ($this->data as $field) {
            $result[$field->getCode()] = $field->getValue();
        }

        return $result;
    }
}
