<?php

namespace App\Features\Reports\Controllers;

use App\Features\Reports\Services\AttendanceReportService;
use App\Features\Reports\Services\ContentProgressReportService;
use App\Features\Reports\Services\QuizReportService;
use App\Features\Reports\Services\VideoQuizReportService;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReportController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected AttendanceReportService $attendanceService,
        protected QuizReportService $quizService,
        protected VideoQuizReportService $videoQuizService,
        protected ContentProgressReportService $contentProgressService
    ) {
        $this->middleware('auth:sanctum');
    }

    // ==================== Attendance Reports ====================

    public function attendanceReport(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $filters = $this->getFilters($request);
            $report = $this->attendanceService->getAllStudentsAttendanceReport($filters);

            return $this->okResponse($report, "Attendance report retrieved successfully");
        }, 'ReportController@attendanceReport');
    }

    public function studentAttendanceReport(Request $request, string $studentId)
    {
        return $this->executeService(function () use ($request, $studentId) {
            $filters = $this->getFilters($request);
            $report = $this->attendanceService->getStudentAttendanceReport($studentId, $filters);

            return $this->okResponse($report, "Student attendance report retrieved successfully");
        }, 'ReportController@studentAttendanceReport');
    }

    // ==================== Quiz Reports ====================

    public function quizReport(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $filters = $this->getFilters($request);
            $report = $this->quizService->getAllStudentsQuizReport($filters);

            return $this->okResponse($report, "Quiz report retrieved successfully");
        }, 'ReportController@quizReport');
    }

    public function studentQuizReport(Request $request, string $studentId)
    {
        return $this->executeService(function () use ($request, $studentId) {
            $filters = $this->getFilters($request);
            $report = $this->quizService->getStudentQuizReport($studentId, $filters);

            return $this->okResponse($report, "Student quiz report retrieved successfully");
        }, 'ReportController@studentQuizReport');
    }

    public function lessonQuizReport(Request $request, string $lessonId)
    {
        return $this->executeService(function () use ($request, $lessonId) {
            $filters = $this->getFilters($request);
            $report = $this->quizService->getLessonQuizReport($lessonId, $filters);

            return $this->okResponse($report, "Lesson quiz report retrieved successfully");
        }, 'ReportController@lessonQuizReport');
    }

    // ==================== Video Quiz Reports ====================

    public function videoQuizReport(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $filters = $this->getFilters($request);
            $report = $this->videoQuizService->getAllStudentsVideoQuizReport($filters);

            return $this->okResponse($report, "Video quiz report retrieved successfully");
        }, 'ReportController@videoQuizReport');
    }

    public function studentVideoQuizReport(Request $request, string $studentId)
    {
        return $this->executeService(function () use ($request, $studentId) {
            $filters = $this->getFilters($request);
            $report = $this->videoQuizService->getStudentVideoQuizReport($studentId, $filters);

            return $this->okResponse($report, "Student video quiz report retrieved successfully");
        }, 'ReportController@studentVideoQuizReport');
    }

    public function lessonVideoQuizReport(Request $request, string $lessonId)
    {
        return $this->executeService(function () use ($request, $lessonId) {
            $filters = $this->getFilters($request);
            $report = $this->videoQuizService->getVideoQuizzesByLesson($lessonId, $filters);

            return $this->okResponse($report, "Lesson video quiz report retrieved successfully");
        }, 'ReportController@lessonVideoQuizReport');
    }

    // ==================== Content Progress Reports ====================

    public function contentProgressReport(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $filters = $this->getFilters($request);
            $report = $this->contentProgressService->getAllStudentsContentProgressReport($filters);

            return $this->okResponse($report, "Content progress report retrieved successfully");
        }, 'ReportController@contentProgressReport');
    }

    public function studentContentProgressReport(Request $request, string $studentId)
    {
        return $this->executeService(function () use ($request, $studentId) {
            $filters = $this->getFilters($request);
            $report = $this->contentProgressService->getStudentContentProgressReport($studentId, $filters);

            return $this->okResponse($report, "Student content progress report retrieved successfully");
        }, 'ReportController@studentContentProgressReport');
    }

    public function lessonContentProgressReport(Request $request, string $lessonId)
    {
        return $this->executeService(function () use ($request, $lessonId) {
            $filters = $this->getFilters($request);
            $report = $this->contentProgressService->getLessonContentProgressReport($lessonId, $filters);

            return $this->okResponse($report, "Lesson content progress report retrieved successfully");
        }, 'ReportController@lessonContentProgressReport');
    }

    public function groupContentProgressReport(Request $request, string $groupId)
    {
        return $this->executeService(function () use ($request, $groupId) {
            $filters = $this->getFilters($request);
            $report = $this->contentProgressService->getGroupContentProgressReport($groupId, $filters);

            return $this->okResponse($report, "Group content progress report retrieved successfully");
        }, 'ReportController@groupContentProgressReport');
    }

    // ==================== Combined Student Report ====================

    public function studentFullReport(Request $request, string $studentId)
    {
        return $this->executeService(function () use ($request, $studentId) {
            $filters = $this->getFilters($request);

            $attendanceReport = $this->attendanceService->getStudentAttendanceReport($studentId, $filters);
            $quizReport = $this->quizService->getStudentQuizReport($studentId, $filters);
            $videoQuizReport = $this->videoQuizService->getStudentVideoQuizReport($studentId, $filters);
            $contentProgressReport = $this->contentProgressService->getStudentContentProgressReport($studentId, $filters);

            return $this->okResponse([
                'student' => $attendanceReport['student'],
                'attendance' => [
                    'summary' => $attendanceReport['summary'],
                    'by_session_type' => $attendanceReport['by_session_type'],
                ],
                'quizzes' => [
                    'summary' => $quizReport['summary'],
                    'by_quiz' => $quizReport['by_quiz'],
                ],
                'video_quizzes' => [
                    'summary' => $videoQuizReport['summary'],
                    'by_video' => $videoQuizReport['by_video'],
                ],
                'content_progress' => [
                    'summary' => $contentProgressReport['summary'],
                    'by_course' => $contentProgressReport['by_course'],
                ],
                'filters' => $this->getFilters($request),
            ], "Student full report retrieved successfully");
        }, 'ReportController@studentFullReport');
    }

    protected function getFilters(Request $request): array
    {
        return [
            'student_id' => $request->input('student_id'),
            'group_id' => $request->input('group_id'),
            'course_id' => $request->input('course_id'),
            'lesson_id' => $request->input('lesson_id'),
            'quiz_id' => $request->input('quiz_id'),
            'video_quiz_id' => $request->input('video_quiz_id'),
            'video_content_id' => $request->input('video_content_id'),
            'session_type' => $request->input('session_type'), // online, offline
            'period' => $request->input('period'), // this_week, this_month
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];
    }
}
