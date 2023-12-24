<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Utils\SearchData;
use App\Http\Requests\Posts\PostSearchRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource for all visitors.
     * Sort, Search, Ordering.
     */
    public function search(PostSearchRequest $request)
    {
        $searchData = new SearchData($request->validated());
        return Post::search(Post::active(), $searchData);
    }

    /**
     * Display a listing of the resource by user.
     * Sort, Search, Ordering.
     */
    public function userSearch(PostSearchRequest $request)
    {
        $searchData = new SearchData($request->validated());
        return Post::search(Post::where('user_id', $request->user()->id), $searchData);
    }

    /**
     * Store a newly created resource in storage.
     * Create.
     */
    public function store(Request $request)
    {
        $post = $request->user()->posts()->create([
            'title' => $request->title,
            'image_url' => $request->image_url,
            'content' => $request->content,
            'is_active' => $request->is_active,
        ]);
        return response()->json($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Set Active, Set Inactive, Update,
     */
    public function update(Request $request, Post $post)
    {
        $post->update([
            'title' => $request->title ?? $post->title,
            'image_url' => $request->image_url ?? $post->image_url,
            'content' => $request->content ?? $post->content,
            'is_active' => $request->is_active ?? $post->is_active,
        ]);
        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     * Delete.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json($post);
    }
}
