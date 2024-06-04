<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'image', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'following' => 'bool'
        ];
    }

    public function article():HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function favoriteArticles()
    {
        return $this->belongsToMany(Article::class, 'article_favorite', 'user_id', 'article_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'followee_id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'followee_id', 'user_id');
    }

    /**
     * Follow a user.
     *
     * @param  User  $user
     * @return bool
     */
    public function follow(User $user): bool
    {
        if (!$this->isFollowing($user)) {
            $this->following()->attach($user->id, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            // Check if the relationship was successfully created
            return $this->isFollowing($user);
        }

        return false;
    }

    /**
     * Unfollow a user.
     *
     * @param  User  $user
     * @return bool
     */
    public function unfollow(User $user): bool
    {
        if ($this->isFollowing($user)) {
            $this->following()->detach($user->id);

            // Check if the relationship was successfully deleted
            return !$this->isFollowing($user);
        }

        return false;
    }

    /**
     * Check if the user is following another user.
     *
     * @param  User  $user
     * @return bool
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()->where('followee_id', $user->id)->exists();
    }

    /**
     * Check if this user is being followed by the authenticated user.
     *
     * @return bool
     */
    public function isFollowedByAuthUser(): bool
    {
        $authUser = auth()->user();
        return $authUser ? $authUser->following()->where('followee_id', $this->id)->exists() : false;
    }
}
