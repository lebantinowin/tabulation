<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Model
{
    use HasFactory, SoftDeletes;

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
