<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleFavorite extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'article_favorite';

    protected $fillable = [
        'user_id',
        'article_slug',
        'article_id',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'articles', 'id');
    }
}
