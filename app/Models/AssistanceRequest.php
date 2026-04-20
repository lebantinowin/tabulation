<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'judge_id',
        'event_id',
        'message',
        'is_confirmed',
        'confirmed_at',
    ];

    protected $casts = [
        'is_confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
