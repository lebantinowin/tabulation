<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'contestant_id',
        'criteria_id',
        'judge_id',
        'score',
        'remarks',
    ];

    public function contestant()
    {
        return $this->belongsTo(Contestant::class);
    }

    public function criteria()
    {
        return $this->belongsTo(Criteria::class);
    }

    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id');
    }
}
