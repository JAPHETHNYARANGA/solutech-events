<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function attendees()
    {
        return $this->hasManyThrough(Attendee::class, Event::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'tenant_id', 'slug');
    }
}
