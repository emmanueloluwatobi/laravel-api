<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $user = request()->user();
        // $posts = $user->posts()->paginate();
        $user = request()->user();
        $posts = $user->posts()->paginate();

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $data['author_id'] = $request->user()->id;

        $post = post::create($data);

        return response()->json(new PostResource($post), 201);
        // ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(post $post)
    {
        abort_if(Auth::id() != $post->author_id, 403, 'Unauthorized');
        
        return new PostResource($post);
        // ->header('Test', 'Emmnanuel')
        // ->header('Test2', 'EmmnanuelTobi')
        // ->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, post $post)
    {

        abort_if(Auth::id() != $post->author_id, 403, 'Unauthorized');
        
        $data = $request->validate([
            'title' => 'required|string|min:2',
            'body' => ['required', 'string', 'min:2'],
        ]);

        $post->update($data);

        return $post;

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post $post)
    {
        abort_if(Auth::id() != $post->author_id, 403, 'Unauthorized');
        
        $post->delete();
        return response()->noContent();
    }
}
