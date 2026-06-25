<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterMeterResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => 'ene_'.$this->id,
            'status' => 'SUCCESS',
            'processedAt' => now()->toISOString(),
            'details' => [
                'meterId' => $this->id,
                'serialNumber' => $this->serial_number,
            ],
        ];
    }
}
