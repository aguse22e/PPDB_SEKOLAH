<?php

namespace App\Http\Requests;

use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $type = DocumentType::find($this->input('document_type_id'));

        // Batas tipe & ukuran file mengikuti pengaturan jenis dokumen di database
        $extensions = $type?->allowed_extensions ?? 'pdf,jpg,jpeg,png';
        $maxKb = ($type?->max_size_mb ?? 2) * 1024;

        return [
            'document_type_id' => ['required', 'exists:document_types,id'],
            'document' => ['required', 'file', 'mimes:' . $extensions, 'max:' . $maxKb],
        ];
    }

    public function messages(): array
    {
        return [
            'document.mimes' => 'Format file tidak sesuai dengan jenis dokumen ini.',
            'document.max' => 'Ukuran file melebihi batas maksimal untuk dokumen ini.',
        ];
    }
}
