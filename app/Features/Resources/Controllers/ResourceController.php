<?php

namespace App\Features\Resources\Controllers;

use App\Features\Resources\Models\Resource;
use App\Features\Resources\Requests\ResourceRequest;
use App\Features\Resources\Transformers\ResourceCollection;
use App\Features\Resources\Transformers\ResourceResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $query = Resource::with(['grade', 'uploader']);

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->has('type')) {
                $query->where('type', $request->get('type'));
            }

            if ($request->has('grade_id')) {
                $query->where('grade_id', $request->get('grade_id'));
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Filter for general resources (no grade)
            if ($request->boolean('general_only')) {
                $query->whereNull('grade_id');
            }

            if ($request->has('page')) {
                $result = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));
                return $this->okResponse(
                    ResourceCollection::make($result),
                    "Resources retrieved successfully"
                );
            }

            return $this->okResponse(
                ResourceResource::collection($query->orderBy('created_at', 'desc')->get()),
                "Resources retrieved successfully"
            );
        }, 'ResourceController@index');
    }

    public function store(ResourceRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $data = $request->validated();
            $data['uploaded_by'] = Auth::id();

            $resource = Resource::create($data);

            if ($request->hasFile('file')) {
                $resource->addMediaFromRequest('file')
                    ->toMediaCollection('resource_file');
            }

            if ($request->hasFile('thumbnail')) {
                $resource->addMediaFromRequest('thumbnail')
                    ->toMediaCollection('resource_thumbnail');
            }

            $resource->load(['grade', 'uploader']);

            return $this->okResponse(
                ResourceResource::make($resource),
                "Resource created successfully"
            );
        }, 'ResourceController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $resource = Resource::with(['grade', 'uploader'])->findOrFail($id);
            $resource->incrementViewCount();

            return $this->okResponse(
                ResourceResource::make($resource),
                "Resource retrieved successfully"
            );
        }, 'ResourceController@show');
    }

    public function update(ResourceRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $resource = Resource::findOrFail($id);
            $resource->update($request->validated());

            if ($request->hasFile('file')) {
                $resource->clearMediaCollection('resource_file');
                $resource->addMediaFromRequest('file')
                    ->toMediaCollection('resource_file');
            }

            if ($request->hasFile('thumbnail')) {
                $resource->clearMediaCollection('resource_thumbnail');
                $resource->addMediaFromRequest('thumbnail')
                    ->toMediaCollection('resource_thumbnail');
            }

            $resource->load(['grade', 'uploader']);

            return $this->okResponse(
                ResourceResource::make($resource),
                "Resource updated successfully"
            );
        }, 'ResourceController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $resource = Resource::findOrFail($id);
            $resource->clearMediaCollection('resource_file');
            $resource->clearMediaCollection('resource_thumbnail');
            $resource->delete();

            return $this->okResponse(
                null,
                "Resource deleted successfully"
            );
        }, 'ResourceController@destroy');
    }

    public function download(string $id)
    {
        return $this->executeService(function () use ($id) {
            $resource = Resource::findOrFail($id);

            if (!$resource->is_downloadable) {
                return $this->errorResponse("This resource is not downloadable", 403);
            }

            $media = $resource->getFirstMedia('resource_file');

            if (!$media) {
                return $this->errorResponse("File not found", 404);
            }

            $resource->incrementDownloadCount();

            return response()->download($media->getPath(), $media->file_name);
        }, 'ResourceController@download');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            return $this->okResponse([
                'types' => [
                    ['value' => 'video', 'label' => 'Video'],
                    ['value' => 'file', 'label' => 'File'],
                    ['value' => 'document', 'label' => 'Document'],
                    ['value' => 'image', 'label' => 'Image'],
                    ['value' => 'audio', 'label' => 'Audio'],
                ],
            ], "Metadata retrieved successfully");
        }, 'ResourceController@metadata');
    }
}
