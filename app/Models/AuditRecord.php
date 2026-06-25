<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['organization_id', 'auditable_type', 'auditable_id', 'action', 'actor_id', 'payload'])]

class AuditRecord extends Model
{
    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
