<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Puedes ajustar la autorización si es necesario
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:courses,id',
            'code' => 'required|string|max:50|unique:groups,code',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'teacher_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'El campo curso es obligatorio.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'code.required' => 'El código del grupo es obligatorio.',
            'code.unique' => 'El código del grupo ya está en uso.',
            'name.required' => 'El nombre del grupo es obligatorio.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.after' => 'La fecha de inicio debe ser posterior a la fecha actual.',
            'end_date.required' => 'La fecha de finalización es obligatoria.',
            'end_date.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
            'teacher_id.required' => 'El campo profesor es obligatorio.',
            'teacher_id.exists' => 'El profesor seleccionado no existe.',
            'teacher_id.integer' => 'El ID del profesor debe ser un número entero.',
        ];
    }
}
