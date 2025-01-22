<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AlphaGreaterValidationRules implements Rule
{
    protected $parameters;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(...$parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $other = request()->input($this->parameters[0]);

        if (isset($this->parameters[1]) && is_numeric($this->parameters[1])) {
            return strcmp(substr($value, 0, $this->parameters[1]), substr($other, 0, $this->parameters[1])) > 0;
        }

        return strcmp($value, $other) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be greater than the :other field.';
    }
}
