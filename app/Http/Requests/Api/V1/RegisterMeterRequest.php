<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Organization;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterMeterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user?->tokenCan('energy_conversion.write')) {
            return false;
        }

        $organizationId = $this->input('organizationId');

        if (! is_numeric($organizationId)) {
            return true;
        }

        if (! Organization::whereKey((int) $organizationId)->exists()) {
            return true;
        }

        return (int) $user->organization_id === (int) $organizationId;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organizationId' => ['required', 'integer', 'exists:organizations,id'],
            'requesterId' => ['required', 'string'],
            'timestamp' => ['required', 'date'],
            'data' => ['required', 'array'],
            'data.serial_number' => ['required', 'string', 'unique:meters,serial_number'],
            'data.type' => ['required', 'string'],
            'data.location' => ['nullable', 'string'],
        ];
    }
}
