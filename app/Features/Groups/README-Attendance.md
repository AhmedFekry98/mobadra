# Attendance System - API Documentation

## نظرة عامة (Overview)

نظام الحضور والغياب يسمح للمدرسين بتسجيل حضور الطلاب في الجلسات (Sessions) التابعة للمجموعات (Groups).

## الـ Routes المتاحة

### 1. Session Attendance Routes
هذه الـ routes مرتبطة بجلسة معينة (`sessionId`)

```
GET    /api/group-sessions/{sessionId}/attendance           → عرض حضور جلسة معينة
POST   /api/group-sessions/{sessionId}/attendance           → تسجيل حضور طالب واحد
POST   /api/group-sessions/{sessionId}/attendance/bulk      → تسجيل حضور عدة طلاب مرة واحدة
POST   /api/group-sessions/{sessionId}/attendance/initialize → تهيئة الحضور لكل طلاب الجلسة
GET    /api/group-sessions/{sessionId}/attendance/stats     → إحصائيات الحضور للجلسة
```

### 2. Standalone Attendance Routes
هذه الـ routes للتعديل على سجل حضور موجود

```
PUT    /api/attendances/{id}  → تعديل سجل حضور
PATCH  /api/attendances/{id}  → تعديل سجل حضور
```

---

## شرح تفصيلي لكل Route

### 1. `GET /api/group-sessions/{sessionId}/attendance`
**الوظيفة:** جلب قائمة حضور جميع الطلاب في جلسة معينة

**Response:**
```json
{
  "success": true,
  "message": "Attendance retrieved successfully",
  "data": [
    {
      "id": 1,
      "student_id": 5,
      "status": "present",
      "attended_at": "2024-12-29T14:00:00",
      "notes": null,
      "student": {
        "id": 5,
        "name": "Ahmed Mohamed"
      }
    },
    {
      "id": 2,
      "student_id": 6,
      "status": "absent",
      "attended_at": null,
      "notes": "مريض",
      "student": {
        "id": 6,
        "name": "Sara Ali"
      }
    }
  ]
}
```

---

### 2. `POST /api/group-sessions/{sessionId}/attendance`
**الوظيفة:** تسجيل حضور طالب واحد

**Request Body:**
```json
{
  "student_id": 5,
  "status": "present",
  "notes": "وصل متأخر 10 دقائق"
}
```

**Status Values:**
- `present` - حاضر
- `absent` - غائب
- `late` - متأخر
- `excused` - غياب بعذر

**Response:**
```json
{
  "success": true,
  "message": "Attendance recorded successfully",
  "data": {
    "id": 1,
    "student_id": 5,
    "status": "present",
    "attended_at": "2024-12-29T14:00:00",
    "notes": "وصل متأخر 10 دقائق",
    "student": {
      "id": 5,
      "name": "Ahmed Mohamed"
    }
  }
}
```

---

### 3. `POST /api/group-sessions/{sessionId}/attendance/bulk`
**الوظيفة:** تسجيل حضور عدة طلاب مرة واحدة (مفيد للمدرس لتسجيل الحضور بسرعة)

**Request Body:**
```json
{
  "attendances": [
    {
      "student_id": 5,
      "status": "present",
      "notes": null
    },
    {
      "student_id": 6,
      "status": "absent",
      "notes": "مريض"
    },
    {
      "student_id": 7,
      "status": "late",
      "notes": "تأخر 15 دقيقة"
    },
    {
      "student_id": 8,
      "status": "excused",
      "notes": "إجازة رسمية"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Attendance recorded successfully",
  "data": [
    {
      "id": 1,
      "student_id": 5,
      "status": "present",
      "student": { "id": 5, "name": "Ahmed" }
    },
    {
      "id": 2,
      "student_id": 6,
      "status": "absent",
      "student": { "id": 6, "name": "Sara" }
    }
  ]
}
```

---

### 4. `POST /api/group-sessions/{sessionId}/attendance/initialize`
**الوظيفة:** تهيئة سجلات الحضور لجميع طلاب المجموعة في الجلسة (يضع الكل كـ absent افتراضياً)

**متى تستخدم؟**
- عند بداية الجلسة، المدرس يضغط "بدء تسجيل الحضور"
- النظام ينشئ سجل حضور لكل طالب في المجموعة بحالة `absent`
- ثم المدرس يغير حالة الحاضرين فقط

**Request:** لا يحتاج body

**Response:**
```json
{
  "success": true,
  "message": "Session attendance initialized successfully",
  "data": [
    {
      "id": 1,
      "student_id": 5,
      "status": "absent",
      "student": { "id": 5, "name": "Ahmed" }
    },
    {
      "id": 2,
      "student_id": 6,
      "status": "absent",
      "student": { "id": 6, "name": "Sara" }
    }
  ]
}
```

---

### 5. `GET /api/group-sessions/{sessionId}/attendance/stats`
**الوظيفة:** جلب إحصائيات الحضور للجلسة

**Response:**
```json
{
  "success": true,
  "message": "Session attendance stats retrieved successfully",
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

---

### 6. `PUT/PATCH /api/attendances/{id}`
**الوظيفة:** تعديل سجل حضور موجود

**متى تستخدم؟**
- لتصحيح خطأ في تسجيل الحضور
- لتغيير حالة طالب من غائب لحاضر أو العكس

**Request Body:**
```json
{
  "status": "present",
  "notes": "تم التصحيح - كان حاضر"
}
```

---

## Sample Use Cases (سيناريوهات الاستخدام)

### السيناريو 1: المدرس يبدأ جلسة جديدة ويسجل الحضور

```javascript
// الخطوة 1: تهيئة الحضور (إنشاء سجلات لكل الطلاب)
POST /api/group-sessions/15/attendance/initialize

// الخطوة 2: تسجيل حضور الطلاب الموجودين (bulk)
POST /api/group-sessions/15/attendance/bulk
{
  "attendances": [
    { "student_id": 5, "status": "present" },
    { "student_id": 6, "status": "present" },
    { "student_id": 7, "status": "late", "notes": "تأخر 10 دقائق" }
  ]
}

// الخطوة 3: عرض إحصائيات الجلسة
GET /api/group-sessions/15/attendance/stats
```

### السيناريو 2: المدرس يسجل حضور طالب واحد وصل متأخر

```javascript
POST /api/group-sessions/15/attendance
{
  "student_id": 8,
  "status": "late",
  "notes": "وصل بعد نصف ساعة"
}
```

### السيناريو 3: تصحيح خطأ في الحضور

```javascript
// الطالب كان مسجل غائب بالخطأ، نصححه لحاضر
PATCH /api/attendances/42
{
  "status": "present",
  "notes": "تم التصحيح"
}
```

### السيناريو 4: عرض حضور جلسة سابقة

```javascript
GET /api/group-sessions/15/attendance
```

---

## Attendance Status Values

| Status | المعنى | الوصف |
|--------|--------|-------|
| `present` | حاضر | الطالب حضر الجلسة |
| `absent` | غائب | الطالب لم يحضر |
| `late` | متأخر | الطالب حضر متأخراً |
| `excused` | غياب بعذر | الطالب غائب بعذر مقبول |

---

## Authorization (الصلاحيات)

- **عرض الحضور:** يحتاج صلاحية `group.view`
- **تسجيل/تعديل الحضور:** يحتاج صلاحية `group.update`

---

## Database Schema

```
attendances
├── id
├── group_id        → المجموعة
├── session_id      → الجلسة
├── student_id      → الطالب
├── status          → present/absent/late/excused
├── attended_at     → وقت الحضور
├── notes           → ملاحظات
├── recorded_by     → من سجل الحضور (المدرس)
├── created_at
└── updated_at
```
