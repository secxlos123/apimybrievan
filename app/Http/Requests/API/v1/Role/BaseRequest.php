<?php

namespace App\Http\Requests\API\v1\Role;

use App\Http\Requests\BaseRequest as FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * Avaliable permissions.
     *
     * @var array
     */
    protected $permissions = [
        'home', 'nasabah', 'properti', 'e-form', 'developer', 'debitur', 'penjadwalan', 
        'kalkulator', 'tracking', 'pihak-ke-3', 'manajemen-user', 'manajemen-role'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'slug' => 'required',
        ];
    }

    /**
     * Get the validation rules of permissions.
     *
     * @return array
     */
    protected function permissions()
    {
        $permissions = [];
        foreach ($this->permissions as $permission) {
            $permissions["permissions.{$permission}"] = 'required|boolean';
        }
        return $permissions;
    }
}
