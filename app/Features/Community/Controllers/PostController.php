<?php

namespace App\Features\Community\Controllers;

use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use App\Features\Community\Requests\CreatePostRequest;
use App\Features\Community\Requests\UpdatePostRequest;
use App\Features\Community\Services\PostService;
use App\Features\Community\Transformers\PostResource;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected PostService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $userId = $request->query('user_id');
            $posts = $this->service->getPosts($userId);

            return $this->okResponse(
                PostResource::collection($posts),
                "Posts retrieved successfully"
            );
        }, 'PostController@index');
    }

    public function store(CreatePostRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $userId = auth()->user()->id;
            $post = $this->service->createPost($userId, $request->validated());

            return $this->okResponse(
                PostResource::make($post),
                "Post created successfully"
            );
        }, 'PostController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $post = $this->service->getPostWithDetails($id);

            return $this->okResponse(
                PostResource::make($post),
                "Post retrieved successfully"
            );
        }, 'PostController@show');
    }

    public function update(UpdatePostRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $post = $this->service->getPostById($id);
            $userId = auth()->user()->id;

            if ($post->user_id !== $userId) {
                return $this->errorResponse('You can only edit your own posts', 403);
            }

            $post = $this->service->updatePost($id, $request->validated());

            return $this->okResponse(
                PostResource::make($post),
                "Post updated successfully"
            );
        }, 'PostController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $post = $this->service->getPostById($id);
            $userId = auth()->user()->id;

            if ($post->user_id !== $userId) {
                return $this->errorResponse('You can only delete your own posts', 403);
            }

            $this->service->deletePost($id);

            return $this->okResponse(null, "Post deleted successfully");
        }, 'PostController@destroy');
    }

    public function toggleLike(string $id)
    {
        return $this->executeService(function () use ($id) {
            $userId = auth()->user()->id;
            $result = $this->service->toggleLike($id, $userId);

            return $this->okResponse(
                $result,
                $result['is_liked'] ? "Post liked" : "Post unliked"
            );
        }, 'PostController@toggleLike');
    }

    public function pin(string $id)
    {
        return $this->executeService(function () use ($id) {
            $post = $this->service->pinPost($id, true);

            return $this->okResponse(
                PostResource::make($post),
                "Post pinned successfully"
            );
        }, 'PostController@pin');
    }

    public function unpin(string $id)
    {
        return $this->executeService(function () use ($id) {
            $post = $this->service->pinPost($id, false);

            return $this->okResponse(
                PostResource::make($post),
                "Post unpinned successfully"
            );
        }, 'PostController@unpin');
    }
}
