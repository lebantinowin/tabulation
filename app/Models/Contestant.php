<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contestant extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contestants';

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'number',
        'image',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        $imagePath = $this->image;
        
        if (!$imagePath) {
            return null;
        }

        $possiblePaths = [];
        
        if (str_contains($imagePath, 'storage/')) {
            $possiblePaths[] = $imagePath;
        } elseif (str_contains($imagePath, 'contestants/')) {
            $possiblePaths[] = 'storage/' . $imagePath;
            $possiblePaths[] = $imagePath;
        } else {
            $possiblePaths[] = 'storage/contestants/' . $imagePath;
            $possiblePaths[] = 'storage/' . $imagePath;
            $possiblePaths[] = 'contestants/' . $imagePath;
            $possiblePaths[] = $imagePath;
        }

        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
