<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'user_id', 'content', 'image','total_likes','total_comments'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post_reacts()
    {
        return $this->hasMany(Reaction::class);
    }

    public function post_comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getCommentAttribute()
    {
        $comments = Comment::where('post_id',$this->id)->get();
        return $comments;
    }

}
