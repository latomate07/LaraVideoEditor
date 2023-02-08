<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'url',
        'size',
        'user_id'
    ];

    /**
     * Get video owner
     * 
     * @return object
     */
    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
