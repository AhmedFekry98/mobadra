# دليل إنشاء Final Quiz للكورس

## نظرة عامة

الـ `final_quiz_id` هو حقل في جدول `courses` يربط الكورس بـ Quiz نهائي. هذا الـ Quiz يكون امتحان نهائي للكورس بالكامل (مش تابع لحصة معينة).

---

## API Endpoints للـ Final Quiz

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/quizzes/course/{courseId}/final` | جلب الـ Final Quiz للكورس |
| POST | `/api/quizzes/course/{courseId}/final` | إنشاء Final Quiz جديد للكورس |
| PUT | `/api/quizzes/course/{courseId}/final` | تحديث الـ Final Quiz |
| DELETE | `/api/quizzes/course/{courseId}/final` | حذف الـ Final Quiz |

---

## هيكل الـ Models

### 1. Course Model
```php
// app/Features/Courses/Models/Course.php

protected $fillable = [
    'term_id',
    'grade_id',
    'title',
    'description',
    'slug',
    'is_active',
    'final_quiz_id',  // ← هنا الربط بالـ Quiz النهائي
];

// العلاقة مع الـ Quiz
public function finalQuiz()
{
    return $this->belongsTo(Quiz::class, 'final_quiz_id');
}
```

### 2. Quiz Model
```php
// app/Features/Courses/Models/Quiz.php

protected $fillable = [
    'time_limit',        // الوقت المحدد بالدقائق
    'passing_score',     // درجة النجاح (نسبة مئوية)
    'max_attempts',      // عدد المحاولات المسموحة
    'shuffle_questions', // خلط الأسئلة
    'show_answers',      // عرض الإجابات بعد الانتهاء
];
```

### 3. QuizQuestion Model
```php
// app/Features/Courses/Models/QuizQuestion.php

protected $fillable = [
    'quiz_id',
    'question',      // نص السؤال
    'type',          // نوع السؤال
    'points',        // الدرجات
    'order',         // الترتيب
    'explanation',   // شرح الإجابة
    'is_active',
];
```

**أنواع الأسئلة المتاحة:**
- `single_choice` - اختيار واحد
- `multiple_choice` - اختيار متعدد
- `true_false` - صح أو خطأ
- `short_answer` - إجابة قصيرة

### 4. QuizQuestionOption Model
```php
// app/Features/Courses/Models/QuizQuestionOption.php

protected $fillable = [
    'question_id',
    'option_text',   // نص الاختيار
    'is_correct',    // هل هذا الاختيار صحيح؟
    'order',         // الترتيب
];
```

---

## كيفية استخدام الـ API

### الخطوة 1: إنشاء Final Quiz للكورس

```http
POST /api/quizzes/course/{courseId}/final
Content-Type: application/json
Authorization: Bearer {token}

{
    "time_limit": 60,
    "passing_score": 50,
    "max_attempts": 3,
    "shuffle_questions": true,
    "show_answers": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Final Quiz created successfully",
    "data": {
        "id": 1,
        "time_limit": 60,
        "passing_score": 50,
        "max_attempts": 3,
        "shuffle_questions": true,
        "show_answers": true
    }
}
```

### الخطوة 2: جلب الـ Final Quiz بواسطة Course ID

```http
GET /api/quizzes/course/{courseId}/final
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Final Quiz retrieved successfully",
    "data": {
        "id": 1,
        "time_limit": 60,
        "passing_score": 50,
        "max_attempts": 3,
        "shuffle_questions": true,
        "show_answers": true,
        "questions": [...]
    }
}
```

### الخطوة 3: تحديث الـ Final Quiz

```http
PUT /api/quizzes/course/{courseId}/final
Content-Type: application/json
Authorization: Bearer {token}

{
    "time_limit": 90,
    "passing_score": 60
}
```

### الخطوة 4: حذف الـ Final Quiz

```http
DELETE /api/quizzes/course/{courseId}/final
Authorization: Bearer {token}
```

### الخطوة 5: إضافة أسئلة للـ Quiz

**API Endpoint:**
```http
POST /api/quizzes/{quizId}/questions
Content-Type: application/json
Authorization: Bearer {token}

{
    "question": "ما هي عاصمة مصر؟",
    "type": "single_choice",
    "points": 10,
    "order": 1,
    "explanation": "القاهرة هي عاصمة جمهورية مصر العربية",
    "options": [
        {"text": "القاهرة", "is_correct": true},
        {"text": "الإسكندرية", "is_correct": false},
        {"text": "الجيزة", "is_correct": false},
        {"text": "أسوان", "is_correct": false}
    ]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Question created successfully",
    "data": {
        "id": 1,
        "quiz_id": 1,
        "question": "ما هي عاصمة مصر؟",
        "type": "single_choice",
        "points": 10,
        "order": 1,
        "options": [...]
    }
}
```

---

## API Endpoints المتاحة للـ Quiz

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/quizzes/{id}` | عرض تفاصيل الـ Quiz |
| POST | `/api/quizzes/{quizId}/questions` | إضافة سؤال |
| PUT | `/api/quizzes/questions/{questionId}` | تعديل سؤال |
| DELETE | `/api/quizzes/questions/{questionId}` | حذف سؤال |
| POST | `/api/quizzes/{quizId}/attempts` | بدء محاولة |
| POST | `/api/quizzes/attempts/{attemptId}/questions/{questionId}` | إرسال إجابة |
| POST | `/api/quizzes/attempts/{attemptId}/complete` | إنهاء المحاولة |
| GET | `/api/quizzes/attempts/{attemptId}` | نتيجة المحاولة |

---

## مثال كامل: إنشاء Final Quiz

### 1. إنشاء Final Quiz للكورس عبر الـ API

```bash
curl -X POST "http://your-domain/api/quizzes/course/1/final" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "time_limit": 60,
    "passing_score": 50,
    "max_attempts": 3,
    "shuffle_questions": true,
    "show_answers": true
}'
```

### 2. جلب الـ Final Quiz بواسطة Course ID

```bash
curl -X GET "http://your-domain/api/quizzes/course/1/final" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. إضافة الأسئلة للـ Final Quiz

```bash
# سؤال 1
curl -X POST "http://your-domain/api/quizzes/1/questions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "question": "ما هو الناتج من 2 + 2؟",
    "type": "single_choice",
    "points": 5,
    "order": 1,
    "options": [
        {"text": "3", "is_correct": false},
        {"text": "4", "is_correct": true},
        {"text": "5", "is_correct": false}
    ]
}'

# سؤال 2 - صح أو خطأ
curl -X POST "http://your-domain/api/quizzes/1/questions" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "question": "الشمس تدور حول الأرض",
    "type": "true_false",
    "points": 5,
    "order": 2,
    "options": [
        {"text": "صح", "is_correct": false},
        {"text": "خطأ", "is_correct": true}
    ]
}'
```

---

## ملاحظات مهمة

1. **الـ Quiz المستقل vs Lesson Content Quiz:**
   - الـ Final Quiz هو Quiz مستقل مرتبط مباشرة بالكورس
   - الـ Lesson Quiz هو Quiz مرتبط بـ Lesson Content

2. **التحقق من الصلاحيات:**
   - تأكد من إضافة الصلاحيات المناسبة في الـ Policy

3. **Migration:**
   - تأكد من وجود عمود `final_quiz_id` في جدول `courses`
   ```php
   $table->foreignId('final_quiz_id')->nullable()->constrained('quizzes')->nullOnDelete();
   ```

---

## هيكل الملفات

```
app/Features/Courses/
├── Controllers/
│   ├── CourseController.php
│   └── QuizController.php
├── Models/
│   ├── Course.php
│   ├── Quiz.php
│   ├── QuizQuestion.php
│   ├── QuizQuestionOption.php
│   ├── QuizAttempt.php
│   └── QuizAnswer.php
├── Services/
│   ├── CourseService.php
│   └── QuizService.php
├── Requests/
│   ├── CourseRequest.php
│   ├── CreateQuestionRequest.php
│   └── FinalQuizRequest.php
└── Routes/
    └── api.php
```

---

## الخلاصة

لإنشاء Final Quiz للكورس:
1. أنشئ Final Quiz عبر `POST /api/quizzes/course/{courseId}/final`
2. أضف الأسئلة عبر `POST /api/quizzes/{quizId}/questions`
3. جلب الـ Final Quiz عبر `GET /api/quizzes/course/{courseId}/final`
4. الطلاب يمكنهم بدء المحاولة عبر `POST /api/quizzes/{quizId}/attempts`
