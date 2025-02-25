<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class PermissionRequest extends FormRequest
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
        ];

        if (! empty($this->permission)) {

            $rules['name'] = 'required|unique:permissions,name,'.$this->permission->id;

        }

        return $rules;

    }

}
