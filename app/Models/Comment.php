<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'post_id',
        'createdBy_user_id',
        'removed'
    ];

    public function post()
    {
        return $this->belongsTo('App\Models\Post', 'post_id');
    }
}
