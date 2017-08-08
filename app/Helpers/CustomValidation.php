<?php 

namespace App\Helpers;

use Illuminate\Validation\Validator;
use App\Models\NewCard;

class CustomValidation extends Validator
{
    use AvailableType;

    /**
     * This for check old password user
     * 
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return boolean
     */
    public function validateHash($attribute, $value, $parameters)
    {
        return \Hash::check($value, $parameters[0]);
    }

    /**
     * This for check email by type role user
     * 
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return boolean
     */
    public function validateEmailByType($attribute, $value, $parameters)
    {
        $user = \App\Models\User::findEmail($value);
        
        if ($user) {
            $role = $user->roles->first()->slug;
            return in_array($role, $this->types[$parameters[0]]);
        }

        return !$user;
    }
}
