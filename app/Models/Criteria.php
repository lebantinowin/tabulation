<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    use HasFactory;

    protected $table = 'criteria';

    protected $fillable = [
        'event_id',
        'name',
        'weight',
        'max_points',
        'description',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }



    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
