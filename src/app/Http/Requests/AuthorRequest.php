<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
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

    public function rules()
    {
        $rules = [
            'name' => 'required|string',
            'surname' => 'required|string',
        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules['name'] = 'sometimes|string';
            $rules['surname'] = 'sometimes|string';
        }

        return $rules;
    }
}
