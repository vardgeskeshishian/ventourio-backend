<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class JsonUniqueTitle implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        public $location,
        public null|int $locationId = null
    )
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
    public function passes($attribute, $value): bool
    {

        $locale = app()->getLocale();

        $result = $this->location
                    ->whereJsonContains("{$attribute}->{$locale}",  $value)
                    ->first();

       if (empty($result) || (!empty($result) && !empty($this->locationId) && ($this->locationId == $result->id))){
           return true;
       }

       return false;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $className = (new \ReflectionClass($this->location))->getShortName();
        return "The {$className} has already exist.";
    }
}
