<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'banner',
        'date',
        'status',
        'is_archived',
    ];

    public function criteria()
    {
        return $this->hasMany(Criteria::class);
    }

    public function contestants()
    {
        return $this->hasMany(Contestant::class);
    }

    public function assistanceRequests()
    {
        return $this->hasMany(AssistanceRequest::class);
    }

    public function judges()
    {
        return $this->hasMany(User::class)->where('role', 'judge');
    }
}
