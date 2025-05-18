<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'action', 'changes'];

    protected $casts = [
        'changes' => 'array',
    ];

    public function event() {
        return $this->belongsTo(Event::class);
    }
}
