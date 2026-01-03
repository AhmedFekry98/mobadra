# Reports API Documentation

## Overview
The Reports API provides endpoints for retrieving attendance, quiz, video quiz, and content progress reports with various filtering options.

**Base URL:** `/api/reports`

**Authentication:** All endpoints require `Bearer Token` authentication.

---

## Common Query Parameters (Filters)

All report endpoints support the following query parameters:

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `period` | string | Predefined period filter | `this_week`, `this_month` |
| `date_from` | date | Start date (YYYY-MM-DD) | `2025-01-01` |
| `date_to` | date | End date (YYYY-MM-DD) | `2025-01-31` |
| `session_type` | string | Session type filter (attendance only) | `online`, `offline` |
| `group_id` | integer | Filter by group ID | `1` |
| `course_id` | integer | Filter by course ID | `1` |
| `lesson_id` | integer | Filter by lesson ID | `1` |

---

## Attendance Reports

### 1. Get All Students Attendance Report
```
GET /api/reports/attendance
```

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `session_type` - `online` | `offline`
- `group_id` - Filter by group

**Response:**
```json
{
  "success": true,
  "message": "Attendance report retrieved successfully",
  "data": {
    "total_students": 25,
    "overall_summary": {
      "total_records": 150,
      "present": 120,
      "absent": 20,
      "late": 10,
      "average_attendance_rate": 86.67
    },
    "students": [
      {
        "student_id": 1,
        "student_name": "Ahmed Mohamed",
        "total_sessions": 10,
        "present": 8,
        "absent": 1,
        "late": 1,
        "attendance_rate": 90.00
      }
    ],
    "filters": {
      "period": "this_month",
      "session_type": "online"
    }
  }
}
```

---

### 2. Get Single Student Attendance Report
```
GET /api/reports/attendance/student/{studentId}
```

**Path Parameters:**
- `studentId` (required) - Student ID

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `session_type` - `online` | `offline`
- `group_id` - Filter by group

**Response:**
```json
{
  "success": true,
  "message": "Student attendance report retrieved successfully",
  "data": {
    "student": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com"
    },
    "summary": {
      "total_sessions": 20,
      "present": 16,
      "absent": 2,
      "late": 1,
      "excused": 1,
      "attendance_rate": 85.00
    },
    "by_session_type": [
      {
        "type": "online",
        "total": 10,
        "present": 8,
        "absent": 1,
        "late": 1,
        "attendance_rate": 90.00
      },
      {
        "type": "offline",
        "total": 10,
        "present": 8,
        "absent": 1,
        "late": 1,
        "attendance_rate": 90.00
      }
    ],
    "details": [
      {
        "id": 1,
        "date": "2025-01-15",
        "status": "present",
        "session": {
          "id": 5,
          "topic": "Introduction to Laravel",
          "type": "online",
          "date": "2025-01-15"
        },
        "group": {
          "id": 1,
          "name": "Group A"
        },
        "notes": null
      }
    ],
    "filters": {}
  }
}
```

---

## Quiz Reports (Lesson Quizzes)

### 3. Get All Students Quiz Report
```
GET /api/reports/quizzes
```

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `course_id` - Filter by course
- `lesson_id` - Filter by lesson

**Response:**
```json
{
  "success": true,
  "message": "Quiz report retrieved successfully",
  "data": {
    "total_students": 25,
    "overall_summary": {
      "total_attempts": 100,
      "passed": 75,
      "failed": 25,
      "average_score": 72.50
    },
    "students": [
      {
        "student_id": 1,
        "student_name": "Ahmed Mohamed",
        "total_attempts": 5,
        "passed": 4,
        "failed": 1,
        "pass_rate": 80.00,
        "average_score": 78.50,
        "total_points_earned": 45
      }
    ],
    "filters": {}
  }
}
```

---

### 4. Get Single Student Quiz Report
```
GET /api/reports/quizzes/student/{studentId}
```

**Path Parameters:**
- `studentId` (required) - Student ID

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `course_id` - Filter by course
- `lesson_id` - Filter by lesson

**Response:**
```json
{
  "success": true,
  "message": "Student quiz report retrieved successfully",
  "data": {
    "student": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com"
    },
    "summary": {
      "total_attempts": 10,
      "passed": 8,
      "failed": 2,
      "pass_rate": 80.00,
      "average_score": 75.50,
      "total_points_earned": 150,
      "total_points_possible": 200
    },
    "by_quiz": [
      {
        "quiz_id": 1,
        "total_attempts": 2,
        "passed": 2,
        "failed": 0,
        "pass_rate": 100.00,
        "average_score": 85.00
      }
    ],
    "attempts": [
      {
        "id": 1,
        "quiz_id": 1,
        "quiz_title": "Chapter 1 Quiz",
        "lesson": "Introduction to Programming",
        "attempt_number": 1,
        "score": 18,
        "total_points": 20,
        "percentage": 90.00,
        "passed": true,
        "completed_at": "2025-01-15 14:30:00"
      }
    ],
    "filters": {}
  }
}
```

---

### 5. Get Lesson Quizzes Report
```
GET /api/reports/quizzes/lesson/{lessonId}
```

**Path Parameters:**
- `lessonId` (required) - Lesson ID

**Query Parameters:**
- `student_id` - Filter by specific student

**Response:**
```json
{
  "success": true,
  "message": "Lesson quiz report retrieved successfully",
  "data": {
    "lesson_id": 1,
    "quizzes": [
      {
        "quiz_id": 1,
        "title": "Chapter 1 Quiz",
        "total_questions": 10,
        "total_points": 20,
        "passing_score": 60,
        "attempts_count": 50,
        "passed_count": 40,
        "average_score": 72.50,
        "highest_score": 100.00,
        "lowest_score": 30.00
      }
    ],
    "filters": {}
  }
}
```

---

## Video Quiz Reports

### 6. Get All Students Video Quiz Report
```
GET /api/reports/video-quizzes
```

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `course_id` - Filter by course
- `lesson_id` - Filter by lesson

**Response:**
```json
{
  "success": true,
  "message": "Video quiz report retrieved successfully",
  "data": {
    "total_students": 25,
    "overall_summary": {
      "total_attempts": 80,
      "passed": 60,
      "failed": 20,
      "average_score": 70.00
    },
    "students": [
      {
        "student_id": 1,
        "student_name": "Ahmed Mohamed",
        "total_attempts": 5,
        "passed": 4,
        "failed": 1,
        "pass_rate": 80.00,
        "average_score": 75.00,
        "total_points_earned": 30
      }
    ],
    "filters": {}
  }
}
```

---

### 7. Get Single Student Video Quiz Report
```
GET /api/reports/video-quizzes/student/{studentId}
```

**Path Parameters:**
- `studentId` (required) - Student ID

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `course_id` - Filter by course
- `lesson_id` - Filter by lesson

**Response:**
```json
{
  "success": true,
  "message": "Student video quiz report retrieved successfully",
  "data": {
    "student": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com"
    },
    "summary": {
      "total_attempts": 8,
      "passed": 6,
      "failed": 2,
      "pass_rate": 75.00,
      "average_score": 72.50,
      "total_points_earned": 45,
      "total_points_possible": 60
    },
    "by_video": [
      {
        "video_quiz_id": 1,
        "video_title": "Introduction Video",
        "total_attempts": 2,
        "passed": 2,
        "failed": 0,
        "pass_rate": 100.00,
        "average_score": 85.00
      }
    ],
    "attempts": [
      {
        "id": 1,
        "video_quiz_id": 1,
        "video_title": "Introduction Video",
        "lesson": "Getting Started",
        "score": 9,
        "total_points": 10,
        "percentage": 90.00,
        "passed": true,
        "completed_at": "2025-01-15 10:30:00"
      }
    ],
    "filters": {}
  }
}
```

---

### 8. Get Lesson Video Quizzes Report
```
GET /api/reports/video-quizzes/lesson/{lessonId}
```

**Path Parameters:**
- `lessonId` (required) - Lesson ID

**Query Parameters:**
- `student_id` - Filter by specific student

**Response:**
```json
{
  "success": true,
  "message": "Lesson video quiz report retrieved successfully",
  "data": {
    "lesson_id": 1,
    "video_quizzes": [
      {
        "video_quiz_id": 1,
        "video_title": "Introduction Video",
        "total_questions": 3,
        "max_questions": 3,
        "passing_score": 60,
        "attempts_count": 40,
        "passed_count": 32,
        "average_score": 75.00,
        "highest_score": 100.00,
        "lowest_score": 33.33
      }
    ],
    "filters": {}
  }
}
```

---

## Content Progress Reports

### 9. Get All Students Content Progress Report
```
GET /api/reports/content-progress
```

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `group_id` - Filter by group
- `course_id` - Filter by course
- `lesson_id` - Filter by lesson

**Response:**
```json
{
  "success": true,
  "message": "Content progress report retrieved successfully",
  "data": {
    "total_students": 25,
    "overall_summary": {
      "total_progress_records": 150,
      "total_completed": 100,
      "total_in_progress": 50,
      "overall_completion_rate": 66.67,
      "average_progress": 72.50,
      "total_watch_time_seconds": 36000,
      "total_watch_time_formatted": "10h 0m 0s"
    },
    "students": [
      {
        "student_id": 1,
        "student_name": "Ahmed Mohamed",
        "student_email": "ahmed@example.com",
        "total_contents": 10,
        "completed_contents": 8,
        "in_progress_contents": 2,
        "completion_rate": 80.00,
        "average_progress": 85.50,
        "total_watch_time_seconds": 3600,
        "total_watch_time_formatted": "1h 0m 0s"
      }
    ],
    "filters": {
      "group_id": 1
    }
  }
}
```

---

### 10. Get Single Student Content Progress Report
```
GET /api/reports/content-progress/student/{studentId}
```

**Path Parameters:**
- `studentId` (required) - Student ID

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `group_id` - Filter by group
- `course_id` - Filter by course
- `lesson_id` - Filter by lesson

**Response:**
```json
{
  "success": true,
  "message": "Student content progress report retrieved successfully",
  "data": {
    "student": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com"
    },
    "summary": {
      "total_contents": 15,
      "completed_contents": 10,
      "in_progress_contents": 5,
      "completion_rate": 66.67,
      "average_progress": 75.00,
      "total_watch_time_seconds": 5400,
      "total_watch_time_formatted": "1h 30m 0s"
    },
    "by_course": [
      {
        "course_id": 1,
        "course_title": "Introduction to Programming",
        "total_contents": 10,
        "completed_contents": 8,
        "completion_rate": 80.00,
        "average_progress": 85.00
      }
    ],
    "by_lesson": [
      {
        "lesson_id": 1,
        "lesson_title": "Getting Started",
        "total_contents": 5,
        "completed_contents": 4,
        "completion_rate": 80.00,
        "average_progress": 90.00
      }
    ],
    "contents": [
      {
        "id": 1,
        "lesson_content_id": 10,
        "content_title": "Introduction Video",
        "lesson_title": "Getting Started",
        "course_title": "Introduction to Programming",
        "group_id": 1,
        "progress_percentage": 100,
        "is_completed": true,
        "watch_time_seconds": 600,
        "watch_time_formatted": "10m 0s",
        "last_position": 600,
        "completed_at": "2025-01-15 14:30:00",
        "updated_at": "2025-01-15 14:30:00"
      }
    ],
    "filters": {}
  }
}
```

---

### 11. Get Lesson Content Progress Report
```
GET /api/reports/content-progress/lesson/{lessonId}
```

**Path Parameters:**
- `lessonId` (required) - Lesson ID

**Query Parameters:**
- `group_id` - Filter by group
- `student_id` - Filter by specific student

**Response:**
```json
{
  "success": true,
  "message": "Lesson content progress report retrieved successfully",
  "data": {
    "lesson": {
      "id": 1,
      "title": "Getting Started",
      "course": "Introduction to Programming"
    },
    "contents": [
      {
        "content_id": 10,
        "title": "Introduction Video",
        "total_students": 25,
        "completed_count": 20,
        "in_progress_count": 5,
        "completion_rate": 80.00,
        "average_progress": 85.00,
        "total_watch_time": 15000
      }
    ],
    "filters": {}
  }
}
```

---

### 12. Get Group Content Progress Report
```
GET /api/reports/content-progress/group/{groupId}
```

**Path Parameters:**
- `groupId` (required) - Group ID

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `course_id` - Filter by course
- `lesson_id` - Filter by lesson

**Response:**
```json
{
  "success": true,
  "message": "Group content progress report retrieved successfully",
  "data": {
    "group": {
      "id": 1,
      "name": "Group A"
    },
    "summary": {
      "total_students": 25,
      "total_progress_records": 250,
      "total_completed": 180,
      "overall_completion_rate": 72.00,
      "average_progress": 78.50
    },
    "students": [
      {
        "student_id": 1,
        "student_name": "Ahmed Mohamed",
        "total_contents": 10,
        "completed_contents": 8,
        "completion_rate": 80.00,
        "average_progress": 85.00,
        "total_watch_time_seconds": 3600,
        "total_watch_time_formatted": "1h 0m 0s"
      }
    ],
    "filters": {}
  }
}
```

---

## Combined Student Report

### 13. Get Full Student Report
```
GET /api/reports/student/{studentId}
```

Returns combined attendance, quiz, video quiz, and content progress data for a single student.

**Path Parameters:**
- `studentId` (required) - Student ID

**Query Parameters:**
- `period` - `this_week` | `this_month`
- `date_from` - Start date
- `date_to` - End date
- `course_id` - Filter by course
- `group_id` - Filter by group

**Response:**
```json
{
  "success": true,
  "message": "Student full report retrieved successfully",
  "data": {
    "student": {
      "id": 1,
      "name": "Ahmed Mohamed",
      "email": "ahmed@example.com"
    },
    "attendance": {
      "summary": {
        "total_sessions": 20,
        "present": 16,
        "absent": 2,
        "late": 1,
        "excused": 1,
        "attendance_rate": 85.00
      },
      "by_session_type": [
        {
          "type": "online",
          "total": 10,
          "present": 8,
          "absent": 1,
          "late": 1,
          "attendance_rate": 90.00
        }
      ]
    },
    "quizzes": {
      "summary": {
        "total_attempts": 10,
        "passed": 8,
        "failed": 2,
        "pass_rate": 80.00,
        "average_score": 75.50,
        "total_points_earned": 150,
        "total_points_possible": 200
      },
      "by_quiz": []
    },
    "video_quizzes": {
      "summary": {
        "total_attempts": 8,
        "passed": 6,
        "failed": 2,
        "pass_rate": 75.00,
        "average_score": 72.50,
        "total_points_earned": 45,
        "total_points_possible": 60
      },
      "by_video": []
    },
    "content_progress": {
      "summary": {
        "total_contents": 15,
        "completed_contents": 10,
        "in_progress_contents": 5,
        "completion_rate": 66.67,
        "average_progress": 75.00,
        "total_watch_time_seconds": 5400,
        "total_watch_time_formatted": "1h 30m 0s"
      },
      "by_course": []
    },
    "filters": {}
  }
}
```

---

## Frontend Integration Examples

### JavaScript/Axios Example

```javascript
import axios from 'axios';

const API_BASE = 'https://your-api.com/api';

// Set auth token
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Get all students attendance for this month
const getMonthlyAttendance = async () => {
  const response = await axios.get(`${API_BASE}/reports/attendance`, {
    params: {
      period: 'this_month',
      session_type: 'online'
    }
  });
  return response.data;
};

// Get single student attendance with date range
const getStudentAttendance = async (studentId, dateFrom, dateTo) => {
  const response = await axios.get(`${API_BASE}/reports/attendance/student/${studentId}`, {
    params: {
      date_from: dateFrom,
      date_to: dateTo
    }
  });
  return response.data;
};

// Get student full report
const getStudentFullReport = async (studentId) => {
  const response = await axios.get(`${API_BASE}/reports/student/${studentId}`, {
    params: {
      period: 'this_month'
    }
  });
  return response.data;
};

// Get quiz report for a specific course
const getCourseQuizReport = async (courseId) => {
  const response = await axios.get(`${API_BASE}/reports/quizzes`, {
    params: {
      course_id: courseId
    }
  });
  return response.data;
};
```

### React Hook Example

```javascript
import { useState, useEffect } from 'react';
import axios from 'axios';

const useAttendanceReport = (filters) => {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchReport = async () => {
      try {
        setLoading(true);
        const response = await axios.get('/api/reports/attendance', { params: filters });
        setData(response.data.data);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchReport();
  }, [JSON.stringify(filters)]);

  return { data, loading, error };
};

// Usage
const AttendanceReport = () => {
  const { data, loading, error } = useAttendanceReport({
    period: 'this_month',
    session_type: 'online'
  });

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div>
      <h2>Attendance Rate: {data.overall_summary.average_attendance_rate}%</h2>
      {data.students.map(student => (
        <div key={student.student_id}>
          {student.student_name}: {student.attendance_rate}%
        </div>
      ))}
    </div>
  );
};
```

---

## Error Responses

All endpoints return errors in this format:

```json
{
  "success": false,
  "message": "Error message here",
  "errors": {}
}
```

### Common HTTP Status Codes:
- `200` - Success
- `401` - Unauthorized (missing or invalid token)
- `403` - Forbidden (no permission)
- `404` - Resource not found
- `422` - Validation error
- `500` - Server error
