# Content Progress API - تتبع مشاهدة الفيديوهات

## الشرح

هذا النظام مسؤول عن تتبع تقدم الطلاب في مشاهدة محتوى الدروس (فيديوهات، ملفات، إلخ).

### الوظائف الرئيسية:

1. **تحديث التقدم أثناء المشاهدة** - يتم استدعاؤه من الـ Frontend كل فترة أثناء مشاهدة الفيديو
2. **جلب تقدم محتوى معين** - لمعرفة أين توقف الطالب
3. **جلب تقدم كل المحتويات في مجموعة** - لعرض نسبة الإنجاز الكلية
4. **تحديد المحتوى كمكتمل** - عند انتهاء الفيديو
5. **جلب تقدم كل الطلاب (للمدرس)** - لمتابعة الطلاب
6. **ملخص التقدم (للمدرس)** - نسبة إنجاز كل طالب في الكورس

---

## API Endpoints

### 1. تحديث التقدم (Student)
```
POST /api/content-progress/update
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "lesson_content_id": 1,
    "group_id": 1,
    "progress_percentage": 45,
    "last_position": 270,
    "watch_time": 30
}
```

**الحقول:**
- `lesson_content_id` (required): ID المحتوى (الفيديو)
- `group_id` (optional): ID المجموعة
- `progress_percentage` (required): نسبة التقدم (0-100)
- `last_position` (required): آخر موضع بالثواني
- `watch_time` (optional): الوقت المضاف بالثواني

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": 5,
        "lesson_content_id": 1,
        "group_id": 1,
        "progress_percentage": 45,
        "watch_time": 270,
        "last_position": 270,
        "is_completed": false,
        "completed_at": null
    },
    "message": "Progress updated successfully"
}
```

---

### 2. جلب تقدم محتوى معين (Student)
```
GET /api/content-progress/content/{lessonContentId}?group_id=1
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": 5,
        "lesson_content_id": 1,
        "group_id": 1,
        "progress_percentage": 45,
        "watch_time": 270,
        "last_position": 270,
        "is_completed": false,
        "completed_at": null
    },
    "message": "Progress retrieved successfully"
}
```

---

### 3. جلب تقدم كل المحتويات في مجموعة (Student)
```
GET /api/content-progress/group/{groupId}
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "lesson_content_id": 1,
            "progress_percentage": 100,
            "is_completed": true
        },
        {
            "id": 2,
            "lesson_content_id": 2,
            "progress_percentage": 45,
            "is_completed": false
        }
    ],
    "message": "Group progress retrieved successfully"
}
```

---

### 4. تحديد المحتوى كمكتمل (Student)
```
POST /api/content-progress/content/{lessonContentId}/complete
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "group_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "progress_percentage": 100,
        "is_completed": true,
        "completed_at": "2024-12-28 15:00:00"
    },
    "message": "Content marked as completed"
}
```

---

### 5. جلب تقدم كل الطلاب في مجموعة (Teacher)
```
GET /api/content-progress/group/{groupId}/students
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "user_id": 5,
            "user": {
                "id": 5,
                "name": "Ahmed"
            },
            "lesson_content_id": 1,
            "progress_percentage": 100,
            "is_completed": true
        },
        {
            "id": 2,
            "user_id": 6,
            "user": {
                "id": 6,
                "name": "Mohamed"
            },
            "lesson_content_id": 1,
            "progress_percentage": 50,
            "is_completed": false
        }
    ],
    "message": "Students progress retrieved successfully"
}
```

---

### 6. ملخص تقدم الطلاب في كورس (Teacher)
```
GET /api/content-progress/group/{groupId}/course/{courseId}/summary
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "student_id": 5,
            "student_name": "Ahmed",
            "completion_percentage": 75.5
        },
        {
            "student_id": 6,
            "student_name": "Mohamed",
            "completion_percentage": 30.0
        }
    ],
    "message": "Progress summary retrieved successfully"
}
```

---

## Test Cases (Postman/Thunder Client)

### Test Case 1: تحديث التقدم أثناء المشاهدة
```bash
curl -X POST "http://127.0.0.1:8000/api/content-progress/update" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lesson_content_id": 1,
    "group_id": 1,
    "progress_percentage": 25,
    "last_position": 150,
    "watch_time": 30
  }'
```

### Test Case 2: تحديث التقدم مرة أخرى (بعد 30 ثانية)
```bash
curl -X POST "http://127.0.0.1:8000/api/content-progress/update" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lesson_content_id": 1,
    "group_id": 1,
    "progress_percentage": 50,
    "last_position": 300,
    "watch_time": 30
  }'
```

### Test Case 3: جلب التقدم الحالي
```bash
curl -X GET "http://127.0.0.1:8000/api/content-progress/content/1?group_id=1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test Case 4: إكمال المحتوى
```bash
curl -X POST "http://127.0.0.1:8000/api/content-progress/content/1/complete" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "group_id": 1
  }'
```

### Test Case 5: جلب كل التقدم في مجموعة
```bash
curl -X GET "http://127.0.0.1:8000/api/content-progress/group/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test Case 6: (Teacher) جلب تقدم كل الطلاب
```bash
curl -X GET "http://127.0.0.1:8000/api/content-progress/group/1/students" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test Case 7: (Teacher) ملخص التقدم
```bash
curl -X GET "http://127.0.0.1:8000/api/content-progress/group/1/course/1/summary" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## Bunny Webhook

```
POST /api/webhooks/bunny
```

هذا الـ endpoint يستقبل webhooks من Bunny CDN (بدون authentication).
يُستخدم لتحديث حالة الفيديوهات بعد رفعها أو معالجتها.

---

## ملاحظات للتطبيق (Frontend)

1. **أثناء مشاهدة الفيديو**: استدعي `POST /content-progress/update` كل 30 ثانية
2. **عند فتح الفيديو**: استدعي `GET /content-progress/content/{id}` لجلب `last_position` واستكمال من نفس النقطة
3. **عند انتهاء الفيديو**: استدعي `POST /content-progress/content/{id}/complete`
4. **في صفحة الكورس**: استدعي `GET /content-progress/group/{id}` لعرض نسبة الإنجاز لكل محتوى
