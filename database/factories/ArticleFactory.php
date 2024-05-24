<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'users_id' => User::inRandomOrder()->first()->id,
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->paragraph,
            'body' => $this->faker->paragraphs(3, true),
            'tag_id' => $this->faker->numberBetween(0, 10),
            'favorited' => $this->faker->boolean,
            'favoritesCount' => $this->numberBetween(0, 100),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function hasTags(): Factory|ArticleFactory
    {
        return $this->afterCreating(function(Article $article){
            $tag = TagsFactory::new()->createOne();
            $article->tags()->attach($tag->id);
        });
    }

}
