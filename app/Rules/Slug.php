<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Slug implements Rule
{
    /**
     * The validation error message
     *
     * @var string
     */
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        if ($this->hasUnderscores($value)) {
            $this->message = trans('validation.no_underscores');

            return false;
        }

        if ($this->startsWithDashes($value)) {
            $this->message = trans('validation.no_starting_dashes');

            return false;
        }

        if ($this->endsWithDashes($value)) {
            $this->message = trans('validation.no_ending_dashes');

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Check if the value has underscores
     *
     * @param string $value
     * @return boolean
     */
    protected function hasUnderscores($value)
    {
        return preg_match('/_/', $value);
    }

    /**
     * Check if the value start with dashes
     *
     * @param string $value
     * @return boolean
     */
    protected function startsWithDashes($value)
    {
        return preg_match('/^-/', $value);
    }

    /**
     * Check if the value end with dashes
     *
     * @param string $value
     * @return boolean
     */
    protected function endsWithDashes($value)
    {
        return preg_match('/-$/', $value);
    }
}
