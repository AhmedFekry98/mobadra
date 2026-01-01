# Acceptance Exam API Documentation

## Overview
The Acceptance Exam feature allows students to take an entrance exam. Each student can only take the exam **once**. After completion, their status is set to "waiting" for admin review.

## Acceptance Exam Status Flow
```
pending → (student completes exam) → waiting → (admin reviews) → accepted/rejected
```

---

## API Endpoints

### Base URL
```
/api/acceptance-exams
```

---

## 1. Exam Management (Admin)

### List All Exams
```http
GET /api/acceptance-exams
```
**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| search | string | Search by title |
| page | integer | Enable pagination |

**Response:**
```json
{
  "success": true,
  "message": "Acceptance exams retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "Grade 4 Acceptance Exam",
      "description": "Entrance exam for Grade 4",
      "grade_id": 1,
      "time_limit": 60,
      "passing_score": 60,
      "max_attempts": 1,
      "shuffle_questions": false,
      "show_answers": false,
      "is_active": true,
      "questions_count": 10,
      "created_at": "2026-01-02T00:00:00.000000Z"
    }
  ]
}
```

### Get Single Exam
```http
GET /api/acceptance-exams/{id}
```

### Create Exam
```http
POST /api/acceptance-exams
```
**Request Body:**
```json
{
  "title": "Grade 4 Acceptance Exam",
  "description": "Entrance exam for Grade 4",
  "grade_id": 1,
  "time_limit": 60,
  "passing_score": 60,
  "max_attempts": 1,
  "shuffle_questions": false,
  "show_answers": false,
  "is_active": true
}
```

### Update Exam
```http
PUT /api/acceptance-exams/{id}
```

### Delete Exam
```http
DELETE /api/acceptance-exams/{id}
```

---

## 2. Question Management (Admin)

### Add Question to Exam
```http
POST /api/acceptance-exams/{examId}/questions
```
**Request Body:**
```json
{
  "question": "What is 2 + 2?",
  "type": "single_choice",
  "points": 1,
  "order": 1,
  "explanation": "Basic addition",
  "options": [
    { "text": "3", "is_correct": false },
    { "text": "4", "is_correct": true },
    { "text": "5", "is_correct": false },
    { "text": "6", "is_correct": false }
  ]
}
```

**Question Types:**
- `single_choice` - One correct answer
- `multiple_choice` - Multiple correct answers
- `true_false` - True/False question
- `short_answer` - Text answer

### Update Question
```http
PUT /api/acceptance-exams/questions/{questionId}
```

### Delete Question
```http
DELETE /api/acceptance-exams/questions/{questionId}
```

---

## 3. Student Exam Flow

### Start Exam (Student)
```http
POST /api/acceptance-exams/{examId}/start
```
**Note:** Each student can only start the exam **ONCE**. If they try again, they will get an error.

**Response:**
```json
{
  "success": true,
  "message": "Exam attempt started successfully",
  "data": {
    "id": 1,
    "acceptance_exam_id": 1,
    "student_id": 5,
    "attempt_number": 1,
    "status": "in_progress",
    "started_at": "2026-01-02T00:00:00.000000Z"
  }
}
```

**Error (if already attempted):**
```json
{
  "success": false,
  "message": "You have already taken this acceptance exam"
}
```

### Submit Answer
```http
POST /api/acceptance-exams/attempts/{attemptId}/questions/{questionId}/answer
```
**Request Body:**
```json
{
  "selected_option_id": 2
}
```
Or for text answer:
```json
{
  "text_answer": "My answer here"
}
```

### Complete Exam
```http
POST /api/acceptance-exams/attempts/{attemptId}/complete
```
**Note:** When completed, the student's `user_information.acceptance_exam` status is automatically updated to `"waiting"`.

**Response:**
```json
{
  "success": true,
  "message": "Exam completed successfully",
  "data": {
    "id": 1,
    "status": "completed",
    "score": 8,
    "total_points": 10,
    "percentage": 80.00,
    "passed": true,
    "completed_at": "2026-01-02T00:30:00.000000Z"
  }
}
```

### Get Attempt Result
```http
GET /api/acceptance-exams/attempts/{attemptId}/result
```

### Get My Attempts (Student)
```http
GET /api/acceptance-exams/my-attempts
```

---

## 4. Student Acceptance Status Management (Admin)

### Get Students by Acceptance Status
```http
GET /api/acceptance-exams/students
```
**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| status | string | Filter by status: `pending`, `waiting`, `accepted`, `rejected` |
| search | string | Search by name or email |
| page | integer | Enable pagination |

**Examples:**
```http
GET /api/acceptance-exams/students?status=waiting
GET /api/acceptance-exams/students?status=waiting&search=john
GET /api/acceptance-exams/students?status=accepted&page=1
```

**Response:**
```json
{
  "success": true,
  "message": "Students retrieved successfully",
  "data": [
    {
      "id": 5,
      "name": "John Doe",
      "email": "john@example.com",
      "user_information": {
        "acceptance_exam": "waiting",
        "grade_id": 1,
        "phone_number": "01234567890"
      }
    }
  ]
}
```

### Update Student Acceptance Status
```http
PUT /api/acceptance-exams/students/{userId}/status
```
**Request Body:**
```json
{
  "status": "accepted"
}
```

**Valid Status Values:**
| Status | Description |
|--------|-------------|
| `pending` | Student has not taken the exam yet |
| `waiting` | Student completed exam, waiting for admin review |
| `accepted` | Admin approved the student |
| `rejected` | Admin rejected the student |

**Response:**
```json
{
  "success": true,
  "message": "Student acceptance status updated successfully",
  "data": {
    "id": 1,
    "user_id": 5,
    "acceptance_exam": "accepted"
  }
}
```

---

## 5. Admin View Exam Attempts

### Get All Attempts for an Exam
```http
GET /api/acceptance-exams/{examId}/attempts
```

---

## Authentication
All endpoints require authentication via `Bearer Token` (Sanctum).

```http
Authorization: Bearer {token}
```

---

## Status Codes
| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |
