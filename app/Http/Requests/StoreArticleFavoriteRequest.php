<?php

namespace App\Http\Requests;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreArticleFavoriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    // public function rules(): array
    // {
    //     return [
    //         'slug' => ['required', 'string', 'max:255'],
    //     ];
    // }

    // protected function prepareForValidation()
    // {
    //     // Get the followee_id from the route parameter
    //     $slug = $this->route('slug');
    //     dd($slug);
    //     $article = Article::where('slug', $slug)->firstOrFail();

    //     // Merge follower_id and followee_id into the request
    //     $this->merge([
    //         'slug' => $article->slug,
    //     ]);
    // }
}
