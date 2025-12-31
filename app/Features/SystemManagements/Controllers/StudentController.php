<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\AuthManagement\Transformers\ProfileCollection;
use App\Features\AuthManagement\Transformers\ProfileResource;
use App\Features\SystemManagements\Exports\StudentsExport;
use App\Features\SystemManagements\Exports\StudentsTemplateExport;
use App\Features\SystemManagements\Imports\StudentsImport;
use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Requests\StudentRequest;
use App\Features\SystemManagements\Services\UserService;
use App\Features\SystemManagements\Metadata\UserMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject your service in constructor
     */
    public function __construct(
        protected UserService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('viewAny', User::class);

            $result = $this->service->getStudentUsers(
                search: $request->get('search'),
                filter: $request->get('filter'),
                sort: [['name', 'asc']],
                paginate: $request->has('page')
            );

            return $this->okResponse(
                $request->has('page')
                    ? ProfileCollection::make($result)
                    : ProfileResource::collection($result),
                "Students retrieved successfully"
            );
        }, 'StudentController@index');
    }

    /**
     * Display the specified student.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $student = $this->service->getStudentUserById($id);
            $this->authorize('view', $student);

            return $this->okResponse(
                ProfileResource::make($student),
                "Student retrieved successfully"
            );
        }, 'StudentController@show');
    }

    /**
     * Store a newly created student.
     */
    public function store(StudentRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', User::class);

            $student = $this->service->createUser($request->validated());

            return $this->okResponse(
                ProfileResource::make($student),
                "Student created successfully"
            );
        }, 'StudentController@store');
    }

    /**
     * Update the specified student.
     */
    public function update(StudentRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $student = $this->service->getStudentUserById($id);
            $this->authorize('update', $student);

            $student = $this->service->updateStudentUser($id, $request->validated());

            return $this->okResponse(
                ProfileResource::make($student),
                "Student updated successfully"
            );
        }, 'StudentController@update');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $student = $this->service->getStudentUserById($id);
            $this->authorize('delete', $student);

            $this->service->deleteStudentUser($id);

            return $this->okResponse(
                null,
                "Student deleted successfully"
            );
        }, 'StudentController@destroy');
    }

    /**
     * Get metadata for students (filters, searches, etc.)
     */
    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', User::class);

            return $this->okResponse(
                UserMetadata::get(),
                "Student metadata retrieved successfully"
            );
        }, 'StudentController@metadata');
    }

    /**
     * Export students to Excel file
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = [
            'search' => $request->get('search'),
            'grade_id' => $request->get('grade_id'),
        ];

        $filename = 'students_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new StudentsExport($filters), $filename);
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $this->authorize('viewAny', User::class);

        return Excel::download(new StudentsTemplateExport(), 'students_import_template.xlsx');
    }

    /**
     * Import students from Excel file
     */
    public function import(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', User::class);

            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            ]);

            $import = new StudentsImport();
            Excel::import($import, $request->file('file'));

            $summary = $import->getImportSummary();

            if ($summary['failed_count'] > 0 && $summary['success_count'] === 0) {
                return $this->errorResponse(
                    'Import failed. No students were imported.',
                    422,
                    $summary
                );
            }

            return $this->okResponse(
                $summary,
                "Import completed. {$summary['success_count']} students imported successfully, {$summary['failed_count']} failed."
            );
        }, 'StudentController@import');
    }
}
