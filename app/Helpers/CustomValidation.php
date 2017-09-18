<?php 

namespace App\Helpers;

use App\Helpers\Traits\AvailableType;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Validation\Validator;

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
        if ($user = User::findEmail($value)) {
            $role = $user->roles->first()->slug;
            return in_array($role, $this->types[$parameters[0]]);
        }

        return !$user;
    }

    /**
     * This for check alpha and spaces
     * 
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return boolean
     */
    public function validateAlphaSpaces($attribute, $value, $parameters)
    {
        return preg_match('/^[\pL\s]+$/u', $value);
    }

    /**
     * This for check alpha and spaces
     * 
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return boolean
     */
    public function validateDeveloperOwned($attribute, $value, $parameters)
    {
        $propertyType = PropertyType::developerOwned($parameters[0], $value);
        return (bool) $propertyType->count();
    }
}
