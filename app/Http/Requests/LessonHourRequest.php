<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonHourRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'order' => 'nullable|integer',
            'description' => 'nullable|string',
        ];

        // For update requests, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['start_time'] = 'sometimes|required|date_format:H:i';
            $rules['end_time'] = 'sometimes|required|date_format:H:i|after:start_time';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return [
            'name.required' => 'Nama jam pelajaran wajib diisi',
            'start_time.required' => 'Waktu mulai wajib diisi',
            'end_time.required' => 'Waktu selesai wajib diisi',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai',
            'start_time.date_format' => 'Format waktu mulai tidak valid',
            'end_time.date_format' => 'Format waktu selesai tidak valid',
        ];
    }
}
