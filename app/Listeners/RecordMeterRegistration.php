<?php

namespace App\Listeners;

use App\Events\MeterRegistered;
use App\Models\AuditRecord;

class RecordMeterRegistration
{
    /**
     * Handle the event.
     */
    public function handle(MeterRegistered $event): void
    {
        $meter = $event->meter;

        AuditRecord::create([
            'organization_id' => $meter->organization_id,
            'auditable_type' => $meter::class,
            'auditable_id' => $meter->id,
            'action' => 'meter.registered',
            'actor_id' => request()->input('requesterId'),
            'payload' => $meter->only(['serial_number', 'type', 'location', 'status']),
        ]);
    }
}
