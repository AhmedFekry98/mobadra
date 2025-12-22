<?php

namespace App\Features\Groups\Middleware;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Repositories\GroupRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGroupCapacity
{
    public function __construct(
        protected GroupRepository $groupRepository
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $groupId = $request->route('groupId') ?? $request->route('group');

        if (!$groupId) {
            return $next($request);
        }

        $group = $this->groupRepository->find($groupId);

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found',
            ], 404);
        }

        if (!$group->hasCapacity()) {
            // Try to find a similar group with capacity
            $similarGroup = $this->groupRepository->findSimilarGroupWithCapacity(
                $group->course_id,
                $group->days,
                $group->start_time,
                $group->end_time
            );

            if ($similarGroup) {
                // Redirect to similar group
                $request->merge(['redirect_group_id' => $similarGroup->id]);
                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => 'Group is full. No similar group with available capacity found.',
                'data' => [
                    'group_id' => $group->id,
                    'max_capacity' => $group->max_capacity,
                    'current_count' => $group->activeStudentsCount(),
                ],
            ], 422);
        }

        return $next($request);
    }
}
