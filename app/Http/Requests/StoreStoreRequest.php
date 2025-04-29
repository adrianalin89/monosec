<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow authenticated users to create stores
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'url' => ['required', 'url', 'max:255', 'unique:stores,url'],
            'platform_type' => ['nullable', 'string'],
            'magento_version' => ['nullable', 'string', 'max:50'],
            'admin_path' => ['nullable', 'string', 'max:255'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'contact_details' => ['nullable', 'string'],
            'developer_details' => ['nullable', 'string'],
        ];
    }
}
