<?php

namespace Lib\Validation;

use Respect\Validation\Validator as V;

class Validator
{
    use \Lib\Traits\Singleton;

    /**
     * Default error messages
     * @var array
     */
    protected $messages = [
        'urlRegex' => 'Wrong URL format',
        'typeIn'   => 'Please fill out this field',

        // fallback-messages
        'notEmpty' => 'Please fill out this field',
        'regex'    => 'Wrong field format',
        'in'       => 'Please pick value from list'
    ];

    /**
     * Fields validation
     * @param  array $fields   [code => value],
     * @param  array $rules    [code => [method => parameters]]
     * @return ValidationResult
     */
    public function validate($fields, $rules = [])
    {
        $result = new ValidationResult();

        foreach ($fields as $field => $value) {
            $errors    = [];
            $newValue  = $value;

            if (is_string($newValue)) {
                $newValue = trim(htmlspecialchars($value));
            }

            if (isset($rules[$field])) {
                try {
                    $newValue = $this->validateField($field, $value, $rules[$field]);
                } catch (ValidationException $e) {
                    $errors[$e->getMethod()] = $e->getMessage();
                }
            }

            $result->addField(
                new ValidationResultField($field, $newValue, $value, $errors)
            );
        }

        return $result;
    }

    /**
     * Add error messages
     * @param array $messages [code => message]
     */
    public function setMessages(array $messages = [])
    {
        $this->messages = array_merge($this->messages, $messages);
    }

    /**
     * @param  string               $field
     * @param  mixed                $value
     * @param  array                $ruleSet validation rules array, i.e.:
     *                                  ['notEmpty' => true]
     * @throws ValidationException
     * @return mixed                new field value
     */
    protected function validateField(string $field, $value, array $ruleSet)
    {
        foreach ($ruleSet as $rule => $params) {
            $result = true;
            $msg    = $this->getMessage($field, $rule);
            switch ($rule) {
                case 'notEmpty':
                    $result = V::notEmpty()->validate($value);
                    break;
                case 'regex':
                    $result = empty($value) || V::regex($params)->validate($value);
                    break;
                case 'in':
                    $result = V::in($params)->validate($value);
                    break;
            }

            if (!$result) {
                throw new ValidationError($rule, $msg);
            }
        }

        return $value;
    }

    /**
     * Get error message from message list
     * @param  string $field field code
     * @param  string $rule  rule code
     * @return string
     */
    protected function getMessage(string $field, string $rule): ?string
    {
        $code = $field . ucfirst($rule);
        return isset($this->messages[$code]) ?
        $this->messages[$code] :
        $this->messages[$rule];
    }

    /**
     * Get all possible validation messages
     * @return array
     */
    public function getMessages() : array
    {
        return $this->messages;
    }
}
