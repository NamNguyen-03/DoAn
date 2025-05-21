<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('user') ?? null;

        // Base email rule, and modify it if this is an update
        $emailRule = 'required|email|unique:users,email';
        if ($id) {
            $emailRule .= ',' . $id;  // Exclude the current user for update
        }

        // Default rules
        $rules = [
            'name' => 'required|min:4',
            'email' => $emailRule,
            'phone' => 'required|numeric',
            'password' => 'nullable|min:6',
        ];

        // If it's an update request and some fields are present, adjust the rules accordingly
        if ($id) {
            // Dynamically adjusting rules for specific fields
            if ($this->name) {
                $rules['name'] = 'min:4';
            }
            if ($this->phone) {
                $rules['phone'] = 'numeric';
            }
            if ($this->password) {
                $rules['password'] = 'min:6';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => ':attribute bắt buộc phải nhập',
            'min' => ':attribute phải từ :min kí tự',
            'email' => ':attribute phải định dạng email',
            'unique' => ':attribute đã tồn tại',
            'numeric' => ':attribute phải là một số',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'phone' => 'Số điện thoại',
        ];
    }
}
