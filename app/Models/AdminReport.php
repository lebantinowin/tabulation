<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReport extends Model
{
    protected $fillable = ['admin_id', 'type', 'title', 'body', 'is_read'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
