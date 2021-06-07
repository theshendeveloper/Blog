<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class MaxWords implements Rule
{

    private $maxWords;

    /**
     * Create a new rule instance.
     *
     * @param $maxWords
     */
    public function __construct($maxWords)
    {
        $this->maxWords = $maxWords;
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
        return str_word_count($value) <= $this->maxWords;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return " تعداد کلمات :attribute نباید بیشتر از ". $this->maxWords ." کلمه باشد.";
    }
}
