<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'message' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:50'],
            'thank_you_message' => ['nullable', 'string'],
            'upload_enabled' => ['sometimes', 'boolean'],
        ];
    }
}
