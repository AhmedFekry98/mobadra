# Quiz & Assignment Feature

نظام الاختبارات والواجبات للطلاب والمدرسين.

## Features

- **Quizzes**: إنشاء اختبارات مع أسئلة متعددة الخيارات
- **Quiz Attempts**: تتبع محاولات الطلاب ونتائجهم
- **Assignments**: واجبات مع تسليم ملفات
- **Grading**: تقييم الواجبات من قبل المدرس

---

## Database Schema

### Tables

| Table | Description |
|-------|-------------|
| `quizzes` | إعدادات الاختبار |
| `quiz_questions` | أسئلة الاختبار |
| `quiz_question_options` | خيارات الإجابة |
| `quiz_attempts` | محاولات الطلاب |
| `quiz_answers` | إجابات الطلاب |
| `assignments` | إعدادات الواجب |
| `assignment_submissions` | تسليمات الطلاب |

---

## API Endpoints

### Quiz Management (Teacher)

```
GET    /api/quizzes/{id}                           # Get quiz with questions
GET    /api/quizzes/{id}/results                   # Get all student results
POST   /api/quizzes/{quizId}/questions             # Add question
PUT    /api/quizzes/questions/{questionId}         # Update question
DELETE /api/quizzes/questions/{questionId}         # Delete question
```

### Quiz Attempts (Student)

```
POST   /api/quizzes/{quizId}/attempts              # Start quiz attempt
POST   /api/quizzes/attempts/{attemptId}/questions/{questionId}  # Submit answer
POST   /api/quizzes/attempts/{attemptId}/complete  # Complete quiz
GET    /api/quizzes/attempts/{attemptId}           # Get attempt result
```

### Assignment Management (Teacher)

```
GET    /api/assignments/{assignmentId}/submissions  # Get all submissions
POST   /api/assignments/submissions/{id}/grade      # Grade submission
```

### Assignment Submissions (Student)

```
GET    /api/assignments/{assignmentId}/my-submission  # Get my submission
POST   /api/assignments/{assignmentId}/submissions    # Create/update submission
POST   /api/assignments/submissions/{id}/submit       # Submit assignment
GET    /api/my-assignments                            # Get all my assignments
```

---

## Usage Examples

### Create Quiz Question (Teacher)

```http
POST /api/quizzes/1/questions
Content-Type: application/json
Authorization: Bearer {token}

{
    "question": "What is 2 + 2?",
    "type": "single_choice",
    "points": 5,
    "explanation": "Basic math addition",
    "options": [
        {"text": "3", "is_correct": false},
        {"text": "4", "is_correct": true},
        {"text": "5", "is_correct": false},
        {"text": "6", "is_correct": false}
    ]
}
```

### Start Quiz Attempt (Student)

```http
POST /api/quizzes/1/attempts
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Quiz attempt started",
    "data": {
        "id": 1,
        "quiz_id": 1,
        "attempt_number": 1,
        "status": "in_progress",
        "started_at": "2025-01-05T10:30:00.000Z"
    }
}
```

### Submit Answer (Student)

```http
POST /api/quizzes/attempts/1/questions/5
Content-Type: application/json
Authorization: Bearer {token}

{
    "selected_option_id": 12
}
```

### Complete Quiz (Student)

```http
POST /api/quizzes/attempts/1/complete
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Quiz completed",
    "data": {
        "id": 1,
        "status": "completed",
        "score": 45,
        "total_points": 50,
        "percentage": 90.00,
        "passed": true
    }
}
```

### Submit Assignment (Student)

```http
POST /api/assignments/1/submissions
Content-Type: multipart/form-data
Authorization: Bearer {token}

content: This is my assignment solution...
attachments[]: (file)
```

### Grade Assignment (Teacher)

```http
POST /api/assignments/submissions/1/grade
Content-Type: application/json
Authorization: Bearer {token}

{
    "score": 85,
    "feedback": "Great work! Minor improvements needed in section 2."
}
```

---

## Question Types

| Type | Description |
|------|-------------|
| `single_choice` | اختيار إجابة واحدة |
| `multiple_choice` | اختيار عدة إجابات |
| `true_false` | صح أو خطأ |
| `short_answer` | إجابة نصية قصيرة |

---

## Submission Status

| Status | Description |
|--------|-------------|
| `draft` | مسودة (لم يتم التسليم) |
| `submitted` | تم التسليم |
| `graded` | تم التقييم |
| `returned` | تم الإرجاع للمراجعة |

---

## File Structure

```
app/Features/Courses/
├── Controllers/
│   ├── QuizController.php
│   └── AssignmentController.php
├── Migrations/
│   ├── 2025_12_23_0001_create_quiz_questions_table.php
│   ├── 2025_12_23_0002_create_quiz_question_options_table.php
│   ├── 2025_12_23_0003_create_quiz_attempts_table.php
│   ├── 2025_12_23_0004_create_quiz_answers_table.php
│   └── 2025_12_23_0005_create_assignment_submissions_table.php
├── Models/
│   ├── Quiz.php
│   ├── QuizQuestion.php
│   ├── QuizQuestionOption.php
│   ├── QuizAttempt.php
│   ├── QuizAnswer.php
│   ├── Assignment.php
│   └── AssignmentSubmission.php
├── Requests/
│   ├── CreateQuestionRequest.php
│   ├── SubmitAnswerRequest.php
│   ├── CreateSubmissionRequest.php
│   └── GradeSubmissionRequest.php
├── Services/
│   ├── QuizService.php
│   └── AssignmentService.php
└── Transformers/
    ├── QuizResource.php
    ├── QuizQuestionResource.php
    ├── QuizQuestionOptionResource.php
    ├── QuizAttemptResource.php
    ├── QuizAnswerResource.php
    └── AssignmentSubmissionResource.php
```

---

## Flow

### Quiz Flow (Student)

1. الطالب يبدأ محاولة جديدة (`POST /quizzes/{id}/attempts`)
2. الطالب يجيب على كل سؤال (`POST /attempts/{id}/questions/{qId}`)
3. الطالب ينهي الاختبار (`POST /attempts/{id}/complete`)
4. النظام يحسب النتيجة تلقائياً
5. الطالب يرى نتيجته

### Assignment Flow (Student)

1. الطالب ينشئ تسليم (`POST /assignments/{id}/submissions`)
2. الطالب يرفع الملفات
3. الطالب يسلم الواجب (`POST /submissions/{id}/submit`)
4. المدرس يقيم الواجب (`POST /submissions/{id}/grade`)
5. الطالب يرى التقييم والملاحظات

---

## Authorization

- **Teachers**: إنشاء/تعديل/حذف الأسئلة، عرض جميع النتائج، تقييم الواجبات
- **Students**: بدء الاختبار، الإجابة، تسليم الواجبات، عرض نتائجهم فقط
