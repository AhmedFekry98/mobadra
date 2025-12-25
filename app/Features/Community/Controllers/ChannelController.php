<?php

namespace App\Features\Community\Controllers;

use App\Features\Community\Models\Channel;
use App\Features\Community\Requests\ChannelRequest;
use App\Features\Community\Services\ChannelService;
use App\Features\Community\Transformers\ChannelCollection;
use App\Features\Community\Transformers\ChannelResource;
use App\Features\Community\Transformers\PostCollection;
use App\Features\Community\Transformers\PostResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChannelController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected ChannelService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $paginate = $request->has('page');
            $channels = $this->service->getAllChannels(true, $paginate);

            return $this->okResponse(
                $paginate
                    ? ChannelCollection::make($channels)
                    : ChannelResource::collection($channels),
                "Channels retrieved successfully"
            );
        }, 'ChannelController@index');
    }

    public function store(ChannelRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $data = $request->validated();

            $channel = $this->service->createChannel($data);

            return $this->createdResponse(
                ChannelResource::make($channel),
                "Channel created successfully"
            );
        }, 'ChannelController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $channel = $this->service->getChannelById($id);

            return $this->okResponse(
                ChannelResource::make($channel->load('posts')),
                "Channel retrieved successfully"
            );
        }, 'ChannelController@show');
    }

    public function update(ChannelRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $channel = $this->service->updateChannel($id, $request->validated());

            return $this->okResponse(
                ChannelResource::make($channel),
                "Channel updated successfully"
            );
        }, 'ChannelController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $this->service->deleteChannel($id);

            return $this->okResponse(
                null,
                "Channel deleted successfully"
            );
        }, 'ChannelController@destroy');
    }

    public function getPosts(Request $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $channel = $this->service->getChannelById($id);
            $paginate = $request->has('page');

            if ($paginate) {
                $posts = $channel->activePosts()->with(['user', 'channel'])->paginate(15);
                return $this->okResponse(
                    PostCollection::make($posts),
                    "Posts for channel retrieved successfully"
                );
            }

            $posts = $channel->activePosts()->with(['user', 'channel'])->get();
            return $this->okResponse(
                PostResource::collection($posts),
                "Posts for channel retrieved successfully"
            );
        }, 'ChannelController@getPosts');
    }
}
