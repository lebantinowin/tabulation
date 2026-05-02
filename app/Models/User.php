<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

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

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        $imagePath = $this->image;
        $placeholder = asset('placeholder.svg');
        
        if (!$imagePath) {
            return $placeholder;
        }

        $possiblePaths = [];
        
        if (str_contains($imagePath, 'storage/')) {
            $possiblePaths[] = $imagePath;
        } elseif (str_contains($imagePath, 'judges/')) {
            $possiblePaths[] = 'storage/' . $imagePath;
            $possiblePaths[] = $imagePath;
        } else {
            $possiblePaths[] = 'storage/judges/' . $imagePath;
            $possiblePaths[] = 'storage/' . $imagePath;
            $possiblePaths[] = 'judges/' . $imagePath;
            $possiblePaths[] = $imagePath;
        }

        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return $placeholder;
    }

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