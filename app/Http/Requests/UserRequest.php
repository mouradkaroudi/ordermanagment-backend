<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {

        $nameRules = ['required'];
        $usernameRules = ['required', 'unique:users'];
        $emailRules = ['required', 'unique:users'];
        $passwordRules = ['required'];
        $roleRules = ['required'];
        $abilitiesRules = ['required', 'array'];

        if(request()->method() == 'PUT') {
            $nameRules = ['sometimes'];
            $usernameRules = ['sometimes', Rule::unique('users')->ignore($this->user->id)];
            $emailRules = ['sometimes', Rule::unique('users')->ignore($this->user->id)];
            $passwordRules = ['sometimes'];
            $roleRules = ['sometimes'];
            $abilitiesRules = ['sometimes', 'array'];    
        }

        return [
            'name' => $nameRules,
            'username' => $usernameRules,
            'email' => $emailRules,
            'password' => $passwordRules,
            'role' => $roleRules,
            'abilities' => $abilitiesRules
        ];
    }
}
