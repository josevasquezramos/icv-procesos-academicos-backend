<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'course_id' => 'required|integer|unique:courses',
            'title' => 'required|string|max:255',
            'name' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'level' => 'nullable|in:basic,intermediate,advanced',
            'course_image' => 'nullable|string|max:255',
            'video_url' => 'nullable|string|max:255',
            'duration' => 'nullable|numeric',
            'sessions' => 'nullable|integer',
            'selling_price' => 'nullable|numeric',
            'discount_price' => 'nullable|numeric',
            'prerequisites' => 'nullable|string',
            'certificate_issuer' => 'nullable|string|max:255',
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
            'course_id.required' => 'El ID del curso es obligatorio.',
            'course_id.integer' => 'El ID del curso debe ser un número entero.',
            'course_id.unique' => 'El ID del curso ya existe.',

            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',

            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede tener más de 200 caracteres.',

            'description.string' => 'La descripción debe ser una cadena de texto.',

            'level.in' => 'El nivel debe ser uno de los siguientes: básico, intermedio o avanzado.',

            'course_image.string' => 'La imagen del curso debe ser una cadena de texto.',
            'course_image.max' => 'La imagen del curso no puede tener más de 255 caracteres.',

            'video_url.string' => 'La URL del video debe ser una cadena de texto.',
            'video_url.max' => 'La URL del video no puede tener más de 255 caracteres.',

            'duration.numeric' => 'La duración debe ser un número.',

            'sessions.integer' => 'Las sesiones deben ser un número entero.',

            'selling_price.numeric' => 'El precio de venta debe ser un número.',

            'discount_price.numeric' => 'El precio con descuento debe ser un número.',

            'prerequisites.string' => 'Los requisitos previos deben ser una cadena de texto.',

            'certificate_issuer.string' => 'El emisor del certificado debe ser una cadena de texto.',
            'certificate_issuer.max' => 'El emisor del certificado no puede tener más de 255 caracteres.',
        ];
    }

}
