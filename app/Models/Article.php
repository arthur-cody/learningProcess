<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
        // protected $table = 'article';
    protected $fillable = [
        "title",
        "user_id",
        "slug",
        "description",
        "body",
        "tagList",
        "favorited",
        "favoritesCount",
    ];

    /**
     * Get the user that owns the article.
     */
    public function users()
    {
        // dd($this->belongsTo(User::class));
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function comment(){
        return $this->hasMany(Comment::class);
    }

}
