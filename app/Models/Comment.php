<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        "users_id",
        "article_id",
        "articleSlug",
        "body",
    ];

    public function article(){
        return $this->belongsTo(Article::class,'article_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
