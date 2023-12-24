<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Utils\SearchData;
use App\Http\Requests\Posts\PostSearchRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(PostSearchRequest $request)
    {
        $searchData = new SearchData($request->validated());
        return Post::search(Post::active(), $searchData);
    }

    /**
     * Display a listing of the resource.
     */
    public function userSearch(PostSearchRequest $request)
    {
        $searchData = new SearchData($request->validated());
        return Post::search(Post::where('user_id', $request->user()->id), $searchData);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
