<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'agreement_accepted',
        'image',
        'login_code',
        'is_active',
        'event_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'login_code',
    ];

    protected function casts(): array
    {
        return [
            'password'           => 'hashed',
            'agreement_accepted' => 'boolean',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isJudge()
    {
        return $this->role === 'judge';
    }

    public static function generateLoginCode()
    {
        return strtoupper(substr(uniqid(), -6));
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'judge_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}