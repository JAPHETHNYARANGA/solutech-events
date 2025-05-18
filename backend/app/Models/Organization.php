<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function admins() {
        return $this->hasMany(Admin::class);
    }

    public function events() {
        return $this->hasMany(Event::class);
    }

    public function attendees() {
        return $this->hasManyThrough(Attendee::class, Event::class);
    }
}
