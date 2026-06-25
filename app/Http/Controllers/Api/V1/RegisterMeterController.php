<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\MeterRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterMeterRequest;
use App\Http\Resources\Api\V1\RegisterMeterResource;
use App\Models\Meter;

class RegisterMeterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterMeterRequest $request): RegisterMeterResource
    {
        $meter = Meter::create([
            'organization_id' => $request->integer('organizationId'),
            'serial_number' => $request->input('data.serial_number'),
            'type' => $request->input('data.type'),
            'location' => $request->input('data.location'),
            'status' => 'active',
        ]);

        MeterRegistered::dispatch($meter, $request->user()->id);

        return new RegisterMeterResource($meter);
    }
}
