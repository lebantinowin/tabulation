<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'contestant_id',
        'total_score',
        'rank',
        'is_locked',
        'message',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function contestant()
    {
        return $this->belongsTo(Contestant::class);
    }
}
