<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        "users_id",
        "slug",
        "description",
        "body",
    ];

    protected $cast = [
        'favorited' => 'bool'
    ];

    /**
     * Get the user that owns the article.
     */
    public function author()
    {
        // dd($this->belongsTo(User::class));
        return $this->belongsTo(User::class, 'users_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function comment(){
        return $this->hasMany(Comment::class, 'articleSlug','slug');
    }

    public function tags():BelongsToMany
    {
        return $this->belongsToMany(Tags::class, 'article_tags', 'article_id', 'tag_id');
    }

    public function authorFavorited()
    {
        return $this->belongsToMany(User::class, 'article_favorite', 'article_id', 'user_id');
    }

    public function favorites():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_favorite', 'article_id');
    }

}
