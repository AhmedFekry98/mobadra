<?php

namespace App\Features\SystemManagements\Controllers;

use App\Enums\AuditAction;
use App\Features\SystemManagements\Models\Audit;
use App\Features\SystemManagements\Requests\AuditRequest;
use App\Features\SystemManagements\Services\AuditService;
use App\Features\SystemManagements\Transformers\AuditCollection;
use App\Features\SystemManagements\Transformers\AuditResource;
use App\Features\SystemManagements\Metadata\AuditMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;

/**
 * Class AuditController
 * @package App\Features\SystemManagements\Controllers
 */
class AuditController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject audit service in constructor
     */
    public function __construct(
        protected AuditService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of audits
     */
    public function index(Request $request): JsonResponse
    {
        // return $this->executeService(function () use ($request) {
            $this->authorize('viewAny', Audit::class);

            $result = $this->service->getAudits(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                $request->has('page')
                    ?  AuditCollection::make($result)
                    : AuditResource::collection($result),
                "Audits retrieved successfully"
            );
        // }, 'AuditController@index');
    }

    /**
     * Display the specified audit
     */
    public function show(string $id): JsonResponse
    {
        return $this->executeService(function () use ($id) {
            $audit = $this->service->getAuditById($id);
            $this->authorize('view', $audit);

            return $this->okResponse(
                AuditResource::make($audit),
                "Audit retrieved successfully"
            );
        }, 'AuditController@show');
    }

    /**
     * Clean up old audits
     */
    public function cleanup(Request $request): JsonResponse
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('cleanup', Audit::class);

            $daysToKeep = $request->get('days_to_keep', 365);
            $deletedCount = $this->service->cleanupOldAudits($daysToKeep);

            return $this->okResponse([
                'deleted_count' => $deletedCount,
                'days_kept' => $daysToKeep,
            ], "Old audits cleaned up successfully");
        }, 'AuditController@cleanup');
    }

    /**
     * Get metadata for audits (filters, searches, etc.)
     */
    public function metadata(): JsonResponse
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Audit::class);

            return $this->okResponse(
                AuditMetadata::get(),
                "Audit metadata retrieved successfully"
            );
        }, 'AuditController@metadata');
    }
}
