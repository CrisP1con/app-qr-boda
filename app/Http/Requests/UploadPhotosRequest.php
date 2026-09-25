<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1', 'max:20'],
            'photos.*' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:20480'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'Seleccioná al menos una fotografía.',
            'photos.array' => 'La selección de fotografías no es válida.',
            'photos.max' => 'Podés subir hasta 20 fotografías por vez.',
            'photos.*.image' => 'Cada archivo debe ser una imagen válida.',
            'photos.*.mimes' => 'Solo se aceptan archivos JPG, JPEG, PNG o WEBP.',
            'photos.*.extensions' => 'La extensión debe ser JPG, JPEG, PNG o WEBP.',
            'photos.*.max' => 'Cada fotografía puede pesar como máximo 20 MB.',
        ];
    }
}
