# 📚 دليل نظام المجموعات (Groups System)

هذا الدليل يشرح كيفية استخدام نظام المجموعات من البداية للنهاية.

---

## 📋 الفهرس

1. [إنشاء مجموعة جديدة](#1-إنشاء-مجموعة-جديدة)
2. [إضافة طلاب للمجموعة](#2-إضافة-طلاب-للمجموعة)
3. [إضافة مدرسين للمجموعة](#3-إضافة-مدرسين-للمجموعة)
4. [إنشاء الحصص (Sessions)](#4-إنشاء-الحصص-sessions)

5. [تسجيل المشاركين في الحصة (Session Participants)](#5-تسجيل-المشاركين-في-الحصة)
6. [تسجيل الحضور والغياب](#6-تسجيل-الحضور-والغياب)
7. [تتبع مشاهدة الفيديوهات](#7-تتبع-مشاهدة-الفيديوهات)
8. [التقارير والإحصائيات](#8-التقارير-والإحصائيات)
9. [إعداد Zoom Webhook](#9-إعداد-zoom-webhook)

---

## 1. إنشاء مجموعة جديدة

### الخطوة الأولى: جلب البيانات المطلوبة (Metadata)

```http
GET /api/groups/metadata
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "courses": [...],
        "location_types": ["online", "physical"],
        "days": ["saturday", "sunday", "monday", ...]
    }
}
```

### الخطوة الثانية: إنشاء المجموعة

```http
POST /api/groups
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "course_id": 1,
    "name": "مجموعة الأحد والثلاثاء - صباحي",
    "max_capacity": 25,
    "days": ["sunday", "tuesday"],
    "start_date": "2025-01-01",
    "end_date": "2025-03-31",
    "start_time": "10:00",
    "end_time": "12:00",
    "location_type": "online",
    "location": null,
    "is_active": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Group created successfully",
    "data": {
        "id": 1,
        "course_id": 1,
        "name": "مجموعة الأحد والثلاثاء - صباحي",
        "max_capacity": 25,
        "days": ["sunday", "tuesday"],
        "start_date": "2025-01-01",
        "end_date": "2025-03-31",
        "start_time": "10:00:00",
        "end_time": "12:00:00",
        "location_type": "online",
        "is_active": true
    }
}
```

---

## 2. إضافة طلاب للمجموعة

### جلب قائمة الطلاب في المجموعة

```http
GET /api/groups/{groupId}/students
Authorization: Bearer {token}
```

### إضافة طالب جديد

```http
POST /api/groups/{groupId}/students
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "student_id": 5
}
```

**Response:**
```json
{
    "success": true,
    "message": "Student added to group successfully",
    "data": {
        "id": 1,
        "group_id": 1,
        "student_id": 5,
        "enrolled_at": "2025-01-01T10:00:00.000000Z",
        "status": "active"
    }
}
```

### تحديث حالة الطالب

```http
PATCH /api/groups/{groupId}/students/{studentId}/status
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "status": "dropped"  // active, dropped, completed
}
```

### حذف طالب من المجموعة

```http
DELETE /api/groups/{groupId}/students/{studentId}
Authorization: Bearer {token}
```

---

## 3. إضافة مدرسين للمجموعة

### جلب قائمة المدرسين في المجموعة

```http
GET /api/groups/{groupId}/teachers
Authorization: Bearer {token}
```

### إضافة مدرس جديد

```http
POST /api/groups/{groupId}/teachers
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "teacher_id": 2,
    "is_primary": true
}
```

**Response:**
```json
{
    "success": true,
    "message": "Teacher assigned to group successfully",
    "data": {
        "id": 1,
        "group_id": 1,
        "teacher_id": 2,
        "assigned_at": "2025-01-01T10:00:00.000000Z",
        "is_primary": true
    }
}
```

### تعيين مدرس كـ Primary

```http
PATCH /api/groups/{groupId}/teachers/{teacherId}/primary
Authorization: Bearer {token}
```

### حذف مدرس من المجموعة

```http
DELETE /api/groups/{groupId}/teachers/{teacherId}
Authorization: Bearer {token}
```

---

## 4. إنشاء الحصص (Sessions)

### جلب قائمة الحصص

```http
GET /api/groups/{groupId}/sessions
Authorization: Bearer {token}
```

### إنشاء حصة جديدة

```http
POST /api/groups/{groupId}/sessions
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "session_date": "2025-01-05",
    "start_time": "10:00",
    "end_time": "12:00",
    "topic": "مقدمة في البرمجة",
    "lesson_content_id": 1,
    "session_type": "online",
    "session_number": 1,
    "meeting_provider": "zoom",
    "meeting_id": "123456789",
    "meeting_password": "abc123",
    "moderator_link": "https://zoom.us/s/123456789?pwd=xxx",
    "attendee_link": "https://zoom.us/j/123456789?pwd=xxx"
}
```

**ملاحظة:** 
- `session_type`: نوع الحصة (`online` أو `physical`)
- `session_number`: رقم الحصة (1-9 مثلاً)
- `moderator_link`: رابط المدرس (Host)
- `attendee_link`: رابط الطلاب (Join)

### تحديث حصة

```http
PUT /api/group-sessions/{sessionId}
Authorization: Bearer {token}
Content-Type: application/json
```

### إلغاء حصة

```http
POST /api/group-sessions/{sessionId}/cancel
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "cancellation_reason": "عطلة رسمية"
}
```

### جلب رابط الانضمام للحصة

```http
GET /api/group-sessions/{sessionId}/join-link
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "role": "student",
        "join_link": "https://zoom.us/j/123456789?pwd=xxx"
    }
}
```

---

## 5. تسجيل المشاركين في الحصة (Session Participants)

هذا القسم يشرح كيفية ربط الطلاب بالحصة مع بيانات Zoom الخاصة بكل طالب.

### 5.1 تسجيل جميع طلاب المجموعة في الحصة

**عند إنشاء حصة online، يجب تسجيل الطلاب كمشاركين:**

```http
POST /api/group-sessions/{sessionId}/participants/register-all
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "25 students registered for session",
    "data": {
        "registered_count": 25
    }
}
```

### 5.2 تسجيل طالب واحد مع بيانات Zoom

```http
POST /api/group-sessions/{sessionId}/participants
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "user_id": 5,
    "zoom_registrant_id": "abc123xyz",
    "join_url": "https://zoom.us/j/123456789?pwd=xxx&tk=abc123"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "session_id": 1,
        "user_id": 5,
        "zoom_registrant_id": "abc123xyz",
        "join_url": "https://zoom.us/j/123456789?pwd=xxx&tk=abc123",
        "status": "registered",
        "first_join_time": null,
        "last_leave_time": null,
        "total_duration": 0
    }
}
```

### 5.3 جلب قائمة المشاركين في الحصة

```http
GET /api/group-sessions/{sessionId}/participants
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
                "name": "أحمد محمد",
                "email": "ahmed@example.com"
            },
            "zoom_registrant_id": "abc123xyz",
            "join_url": "https://zoom.us/j/123456789?pwd=xxx&tk=abc123",
            "status": "joined",
            "first_join_time": "2025-01-05T10:02:00.000000Z",
            "last_leave_time": null,
            "total_duration": 45
        }
    ]
}
```

### 5.4 جلب رابط الانضمام الخاص بالطالب

```http
GET /api/group-sessions/{sessionId}/participants/my-link
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "join_url": "https://zoom.us/j/123456789?pwd=xxx&tk=abc123",
        "session": {
            "id": 1,
            "topic": "مقدمة في البرمجة",
            "session_date": "2025-01-05",
            "start_time": "10:00",
            "end_time": "12:00"
        }
    }
}
```

### 5.5 آلية تتبع الحضور التلقائي من Zoom

```
┌─────────────────────────────────────────────────────────────────┐
│  1. تسجيل الطلاب كمشاركين في الحصة                              │
│     POST /api/group-sessions/{id}/participants/register-all     │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│  2. الطالب يدخل الـ Meeting من join_url الخاص به               │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│  3. Zoom يرسل Webhook: meeting.participant_joined               │
│     - يتم تسجيل first_join_time                                 │
│     - status يتحول لـ "joined"                                  │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│  4. Zoom يرسل Webhook: meeting.participant_left                 │
│     - يتم تسجيل last_leave_time                                 │
│     - يتم حساب total_duration                                   │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│  5. Zoom يرسل Webhook: meeting.ended                            │
│     - يتم مزامنة جميع المشاركين مع جدول attendances            │
│     - الحضور يتحدد بناءً على نسبة الحضور:                       │
│       * >= 75% → present                                        │
│       * >= 25% → late                                           │
│       * < 25%  → absent                                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## 6. تسجيل الحضور والغياب

### 6.1 تهيئة الحضور للحصة (Initialize)

**قبل بدء الحصة، يجب تهيئة سجلات الحضور لجميع الطلاب:**

```http
POST /api/group-sessions/{sessionId}/attendance/initialize
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Attendance initialized for 25 students",
    "data": {
        "initialized_count": 25
    }
}
```

### 5.2 جلب قائمة الحضور للحصة

```http
GET /api/group-sessions/{sessionId}/attendance
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "student_id": 5,
            "student_name": "أحمد محمد",
            "status": "absent",
            "attended_at": null,
            "notes": null
        },
        {
            "id": 2,
            "student_id": 6,
            "student_name": "محمد علي",
            "status": "present",
            "attended_at": "2025-01-05T10:05:00.000000Z",
            "notes": null
        }
    ]
}
```

### 5.3 تسجيل حضور طالب واحد

```http
POST /api/group-sessions/{sessionId}/attendance
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "student_id": 5,
    "status": "present",
    "notes": "حضر متأخر 5 دقائق"
}
```

**الحالات المتاحة (status):**
- `present` - حاضر
- `absent` - غائب
- `late` - متأخر
- `excused` - غياب بعذر

### 5.4 تسجيل حضور مجموعة طلاب (Bulk)

```http
POST /api/group-sessions/{sessionId}/attendance/bulk
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "attendances": [
        {"student_id": 5, "status": "present"},
        {"student_id": 6, "status": "present"},
        {"student_id": 7, "status": "absent"},
        {"student_id": 8, "status": "late", "notes": "تأخر 10 دقائق"},
        {"student_id": 9, "status": "excused", "notes": "مريض"}
    ]
}
```

### 5.5 تحديث سجل حضور

```http
PUT /api/attendances/{attendanceId}
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "status": "excused",
    "notes": "تم قبول العذر"
}
```

### 5.6 إحصائيات الحضور للحصة

```http
GET /api/group-sessions/{sessionId}/attendance/stats
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_students": 25,
        "present": 20,
        "absent": 3,
        "late": 1,
        "excused": 1,
        "attendance_rate": 80.0
    }
}
```

### 5.7 تقرير الحضور للمجموعة

```http
GET /api/groups/{groupId}/attendance-report
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "group_id": 1,
        "total_sessions": 10,
        "students": [
            {
                "student_id": 5,
                "student_name": "أحمد محمد",
                "present_count": 8,
                "absent_count": 1,
                "late_count": 1,
                "excused_count": 0,
                "attendance_rate": 80.0
            }
        ]
    }
}
```

---

## 6. تتبع مشاهدة الفيديوهات

### 6.1 تحديث Progress أثناء المشاهدة

**يُرسل من Frontend كل 30 ثانية أثناء مشاهدة الفيديو:**

```http
POST /api/content-progress/update
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
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
- `lesson_content_id`: ID المحتوى (الفيديو)
- `group_id`: ID المجموعة (اختياري)
- `progress_percentage`: نسبة المشاهدة (0-100)
- `last_position`: آخر موضع بالثواني
- `watch_time`: الوقت المضاف بالثواني (عادة 30)

**Response:**
```json
{
    "success": true,
    "message": "Progress updated successfully",
    "data": {
        "id": 1,
        "user_id": 5,
        "lesson_content_id": 1,
        "group_id": 1,
        "progress_percentage": 45,
        "watch_time": 270,
        "last_position": 270,
        "is_completed": false,
        "completed_at": null,
        "last_watched_at": "2025-01-05T10:30:00.000000Z"
    }
}
```

### 6.2 جلب Progress لمحتوى معين

```http
GET /api/content-progress/content/{lessonContentId}?group_id=1
Authorization: Bearer {token}
```

### 6.3 جلب كل Progress في مجموعة

```http
GET /api/content-progress/group/{groupId}
Authorization: Bearer {token}
```

### 6.4 تعليم المحتوى كمكتمل

```http
POST /api/content-progress/content/{lessonContentId}/complete
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "group_id": 1
}
```

### 6.5 جلب Progress كل الطلاب (للمدرس)

```http
GET /api/content-progress/group/{groupId}/students
Authorization: Bearer {token}
```

### 6.6 ملخص التقدم للمجموعة (للمدرس)

```http
GET /api/content-progress/group/{groupId}/course/{courseId}/summary
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "student_id": 5,
            "student_name": "أحمد محمد",
            "completion_percentage": 75.5
        },
        {
            "student_id": 6,
            "student_name": "محمد علي",
            "completion_percentage": 60.0
        }
    ]
}
```

---

## 7. التقارير والإحصائيات

### جلب دروس المجموعة

```http
GET /api/groups/{groupId}/lessons
Authorization: Bearer {token}
```

### جلب كل الدروس

```http
GET /api/groups/lessons
Authorization: Bearer {token}
```

### جلب مجموعات كورس معين

```http
GET /api/groups/course/{courseId}
Authorization: Bearer {token}
```

---

## 🔄 Flow كامل للنظام

```
┌─────────────────────────────────────────────────────────────────┐
│                         1. إنشاء المجموعة                        │
│                    POST /api/groups                              │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    2. إضافة الطلاب والمدرسين                     │
│         POST /api/groups/{id}/students                          │
│         POST /api/groups/{id}/teachers                          │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                      3. إنشاء الحصص                              │
│              POST /api/groups/{id}/sessions                      │
│         (8 حصص online + 1 حصة physical)                         │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    4. تهيئة الحضور قبل الحصة                     │
│     POST /api/group-sessions/{id}/attendance/initialize          │
└─────────────────────────────────────────────────────────────────┘
                                │
                ┌───────────────┴───────────────┐
                ▼                               ▼
┌───────────────────────────┐   ┌───────────────────────────────┐
│     حصة Online            │   │      حصة Physical             │
│  - الطالب يدخل من         │   │  - المدرس يسجل الحضور         │
│    attendee_link          │   │    من الداشبورد               │
│  - Bunny يرسل webhook     │   │  POST .../attendance/bulk     │
│  - يتسجل تلقائياً         │   │                               │
└───────────────────────────┘   └───────────────────────────────┘
                │                               │
                └───────────────┬───────────────┘
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                  5. تتبع مشاهدة الفيديوهات                       │
│           POST /api/content-progress/update                      │
│              (كل 30 ثانية من Frontend)                          │
└─────────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                     6. التقارير والمتابعة                        │
│    - تقرير الحضور: GET /api/groups/{id}/attendance-report       │
│    - تقدم الطلاب: GET /api/content-progress/group/{id}/summary  │
└─────────────────────────────────────────────────────────────────┘
```

---

## ⚙️ إعداد Bunny Webhook

لتفعيل تسجيل المشاهدات التلقائي من Bunny:

1. اذهب إلى Bunny Dashboard
2. اختر Video Library
3. اذهب إلى Settings → Webhooks
4. أضف URL:
   ```
   https://your-domain.com/api/webhooks/bunny
   ```
5. (اختياري) أضف Webhook Secret في `.env`:
   ```
   BUNNY_WEBHOOK_SECRET=your_secret_here
   ```

---

## 📝 ملاحظات مهمة

1. **الحضور التلقائي للحصص Online:**
   - عند استخدام Bunny Stream، يتم تسجيل المشاهدة تلقائياً
   - يمكن ربط المشاهدة بالحضور عبر تخصيص إضافي

2. **الحضور اليدوي للحصص Physical:**
   - يجب على المدرس تسجيل الحضور من الداشبورد
   - استخدم `bulk` endpoint لتسجيل حضور جميع الطلاب مرة واحدة

3. **تتبع الفيديوهات:**
   - يُعتبر المحتوى مكتمل عند وصول `progress_percentage` لـ 90%
   - يتم تسجيل `watch_time` تراكمياً

4. **أنواع الحصص:**
   - `online`: حصة عبر الإنترنت (Zoom/Meet)
   - `physical`: حصة حضورية في المركز
