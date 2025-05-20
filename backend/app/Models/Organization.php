<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'data'];

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function attendees()
    {
        return $this->hasManyThrough(Attendee::class, Event::class);
    }

   
}
