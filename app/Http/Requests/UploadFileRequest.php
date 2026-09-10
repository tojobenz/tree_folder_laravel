<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadFileRequest extends FormRequest
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
        return [
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,txt,jpg,png,jpeg',
            'path' => 'required|string|max:255',
            'msg' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'file.required' => 'Le fichier est requis',
            'file.file' => 'Le fichier doit être un fichier valide',
            'file.max' => 'Le fichier ne doit pas dépasser 10MB',
            'file.mimes' => 'Le fichier doit être de type: pdf, doc, docx, txt, jpg, png, jpeg',
            'path.required' => 'Le chemin de destination est requis',
            'path.max' => 'Le chemin ne doit pas dépasser 255 caractères',
            'msg.max' => 'Le message ne doit pas dépasser 500 caractères',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Sanitize the path to prevent path traversal attacks.
     *
     * @param string $path
     * @return string
     */
    public function sanitizePath($path)
    {
        // Remove .. and other dangerous characters
        $path = str_replace('..', '', $path);
        $path = preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $path);
        
        // Ensure path starts with /
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        
        return $path;
    }

    /**
     * Sanitize the filename.
     *
     * @param string $filename
     * @return string
     */
    public function sanitizeFilename($filename)
    {
        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $filename);
        
        // Remove special characters except letters, numbers, underscores, hyphens, and dots
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $filename);
        
        return $filename;
    }
}
