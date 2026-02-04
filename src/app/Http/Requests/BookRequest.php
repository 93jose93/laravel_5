<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
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
            'title' => 'required|string',
            'description' => 'nullable|string',
            'published_date' => 'required|date',
            'author_id' => 'required|exists:authors,id',
        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules['title'] = 'sometimes|string';
            $rules['published_date'] = 'sometimes|date';
            $rules['author_id'] = 'sometimes|exists:authors,id';
        }

        return $rules;
    }
}
