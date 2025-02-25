<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules =  [
            'name'    => 'required|unique:roles,name',
            'permission' => 'nullable|array',
        ];

        if (! empty($this->role)) {

            $rules['name'] = 'required|unique:roles,name,'.$this->role->id;

        }

        return $rules;

    }

}
