<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'author',
        'status',
        'createdBy_user_id',
        'publishedDate'
    ];

    public function author()
    {
        return $this->belongsTo('App\Models\User', 'createdBy_user_id');
    }

    public function lastEditor()
    {
        return $this->belongsTo('App\Models\User', 'updatedBy_user_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Model\Comment');
    }


}
