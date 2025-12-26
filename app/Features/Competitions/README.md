# Competition Module - دليل الاستخدام

## 📋 نظرة عامة

نظام المسابقات يتكون من عدة مراحل:
1. **المرحلة الأولى**: التأهيل الفردي (300 نقطة)
2. **المرحلة الثانية**: التحدي التطبيقي (100 نقطة)
3. **المرحلة الثالثة**: تشكيل الفرق
4. **المرحلة الرابعة**: مشروع الفريق (200 نقطة)
5. **الهاكاثون**: المسابقة النهائية

---

## 🚀 خطوات العمل

### الخطوة 1: إنشاء المسابقة

```http
POST /api/competitions
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "Tech Innovation 2025",
    "name_ar": "الابتكار التقني 2025",
    "description": "مسابقة سنوية للطلاب في مجال التكنولوجيا",
    "start_date": "2025-01-01",
    "end_date": "2025-04-22"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Tech Innovation 2025",
        "name_ar": "الابتكار التقني 2025",
        "status": "upcoming",
        "total_participants": 0,
        "qualified_count": 0,
        "teams_count": 0
    }
}
```

---

### الخطوة 2: إضافة مراحل المسابقة

```http
POST /api/competitions/1/phases
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "phase_number": 1,
    "title": "Individual Qualification",
    "title_ar": "التأهيل الفردي",
    "description": "التعلم الذاتي والاختبارات",
    "start_date": "2025-01-01",
    "end_date": "2025-02-15",
    "max_points": 300
}
```

**المراحل المطلوبة:**

| المرحلة | العنوان | النقاط |
|---------|---------|--------|
| 1 | التأهيل الفردي | 300 |
| 2 | التحدي التطبيقي | 100 |
| 3 | تشكيل الفرق | 0 |
| 4 | مشروع الفريق | 200 |

---

### الخطوة 3: تفعيل المسابقة

```http
PATCH /api/competitions/1
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "status": "active"
}
```

---

### الخطوة 4: تسجيل المشاركين

> **ملاحظة:** يتم تسجيل المشاركين من خلال نظام التسجيل الخاص بالتطبيق

**جلب قائمة المشاركين:**
```http
GET /api/competitions/1/participants?governorate=Cairo&status=registered
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "أحمد محمد",
            "email": "ahmed@example.com",
            "governorate": "Cairo",
            "status": "registered",
            "phase1_score": 0,
            "phase2_score": 0,
            "phase3_score": 0,
            "total_score": 0,
            "rank": null,
            "tier": "Emerging"
        }
    ]
}
```

---

### الخطوة 5: المرحلة الأولى - التأهيل الفردي

#### نظام النقاط (300 نقطة):

| المكون | النقاط | الوصف |
|--------|--------|-------|
| مشاهدة الفيديوهات | 80 | حسب نسبة المشاهدة |
| الاختبارات | 120 | اختبارات الوحدات |
| الاختبار النهائي | 100 | اختبار شامل |

**نقاط مشاهدة الفيديو:**
| نسبة المشاهدة | النقاط |
|---------------|--------|
| 25% | 2 |
| 50% | 4 |
| 75% | 6 |
| 100% | 8 |

> **ملاحظة:** يتم تحديث النقاط تلقائياً من نظام تتبع الفيديوهات والاختبارات

---

### الخطوة 6: تحديث حالة المشاركين للتأهيل

بعد انتهاء المرحلة الأولى، يتم تحديث حالة المشاركين المؤهلين:

```http
PATCH /api/competitions/1/participants/1/status
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "status": "qualified"
}
```

**الحالات المتاحة:**
- `registered`: مسجل
- `qualified`: مؤهل
- `eliminated`: مستبعد
- `pending`: قيد المراجعة

---

### الخطوة 7: المرحلة الثانية - التحدي التطبيقي

#### تقديم الفيديو من المشارك:
> يتم من خلال التطبيق

#### تقييم الفيديو من المحكم:

```http
POST /api/competitions/1/evaluations/phase2/1
Authorization: Bearer {judge_token}
Content-Type: application/json

{
    "idea_clarity": 22,
    "technical_understanding": 20,
    "logic_analysis": 23,
    "presentation_communication": 21,
    "feedback": "عرض جيد، يحتاج تعمق تقني أكثر"
}
```

**معايير التقييم (100 نقطة):**

| المعيار | النقاط القصوى |
|---------|---------------|
| وضوح الفكرة | 25 |
| الفهم التقني | 25 |
| المنطق والتحليل | 25 |
| العرض والتواصل | 25 |

---

### الخطوة 8: عرض لوحة المتصدرين

```http
GET /api/competitions/1/leaderboard?governorate=Cairo&limit=100
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "rank": 1,
            "participant_id": 1,
            "name": "أحمد محمد",
            "governorate": "Cairo",
            "phase1_score": 285,
            "phase2_score": 92,
            "phase3_score": 0,
            "total_score": 377
        }
    ]
}
```

---

### الخطوة 9: المرحلة الثالثة - تشكيل الفرق

#### تصنيف المشاركين (Tier):

بناءً على مجموع نقاط المرحلة الأولى والثانية (400 نقطة):
- **High Performers**: أعلى 33%
- **Mid Performers**: الوسط 34%
- **Emerging Performers**: أدنى 33%

#### تشكيل الفرق تلقائياً:

```http
POST /api/competitions/1/teams/auto-form
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "governorate": "Cairo"
}
```

**تكوين الفريق (5 أعضاء):**
- 2 High Performers
- 2 Mid Performers
- 1 Emerging Performer

**Response:**
```json
{
    "success": true,
    "message": "5 teams formed successfully",
    "data": [
        {
            "id": 1,
            "name": "Team Cairo #1",
            "track": "online",
            "governorate": "Cairo",
            "members": [
                {"name": "أحمد", "role": "Research", "tier": "High"},
                {"name": "محمد", "role": "Research", "tier": "High"},
                {"name": "علي", "role": "Research", "tier": "Mid"},
                {"name": "عمر", "role": "Research", "tier": "Mid"},
                {"name": "خالد", "role": "Research", "tier": "Emerging"}
            ]
        }
    ]
}
```

#### أو إنشاء فريق يدوياً:

```http
POST /api/competitions/1/teams
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "Tech Innovators",
    "track": "online",
    "governorate": "Cairo",
    "member_ids": [1, 2, 3, 4, 5]
}
```

---

### الخطوة 10: تحديث بيانات الفريق

```http
PATCH /api/competitions/1/teams/1
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "project_title": "منصة التعليم الذكي",
    "project_description": "نظام تعليمي يعتمد على الذكاء الاصطناعي"
}
```

---

### الخطوة 11: المرحلة الرابعة - تقييم مشروع الفريق

```http
POST /api/competitions/1/evaluations/team/1
Authorization: Bearer {judge_token}
Content-Type: application/json

{
    "idea_strength": 35,
    "implementation": 38,
    "teamwork": 28,
    "problem_solving": 36,
    "final_presentation": 45,
    "feedback": "مشروع ممتاز بنهج مبتكر"
}
```

**معايير التقييم (200 نقطة):**

| المعيار | النقاط القصوى |
|---------|---------------|
| قوة الفكرة | 40 |
| التنفيذ | 40 |
| العمل الجماعي | 30 |
| حل المشكلات | 40 |
| العرض النهائي | 50 |

---

### الخطوة 12: إعداد الهاكاثون

#### إضافة أيام الهاكاثون:

```http
POST /api/competitions/1/hackathon
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "day_number": 1,
    "title": "Governorate Level",
    "title_ar": "مستوى المحافظة",
    "description": "تتنافس الفرق داخل محافظتها",
    "date": "2025-04-20",
    "level": "governorate"
}
```

**مستويات الهاكاثون:**
- `governorate`: مستوى المحافظة
- `national`: المستوى الوطني
- `final`: النهائي

#### تحديث حالة يوم الهاكاثون:

```http
PATCH /api/competitions/1/hackathon/1
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "status": "active",
    "teams_count": 40
}
```

---

### الخطوة 13: إضافة المحكمين

```http
POST /api/competitions/1/judges
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "name": "د. أحمد حسن",
    "email": "ahmed.hassan@example.com",
    "specialty": "الذكاء الاصطناعي وتعلم الآلة"
}
```

---

### الخطوة 14: إنهاء المسابقة

```http
PATCH /api/competitions/1
Authorization: Bearer {admin_token}
Content-Type: application/json

{
    "status": "completed"
}
```

---

## 📊 ملخص الـ APIs

### المسابقات
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/competitions` | جلب كل المسابقات |
| POST | `/competitions` | إنشاء مسابقة |
| GET | `/competitions/{id}` | جلب مسابقة |
| PATCH | `/competitions/{id}` | تحديث مسابقة |
| DELETE | `/competitions/{id}` | حذف مسابقة |

### المراحل
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/competitions/{id}/phases` | جلب المراحل |
| POST | `/competitions/{id}/phases` | إضافة مرحلة |
| PATCH | `/competitions/{id}/phases/{phaseId}` | تحديث مرحلة |

### المشاركين
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/competitions/{id}/participants` | جلب المشاركين |
| GET | `/competitions/{id}/participants/{participantId}` | جلب مشارك |
| PATCH | `/competitions/{id}/participants/{participantId}/status` | تحديث الحالة |
| GET | `/competitions/{id}/leaderboard` | لوحة المتصدرين |

### الفرق
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/competitions/{id}/teams` | جلب الفرق |
| POST | `/competitions/{id}/teams` | إنشاء فريق |
| POST | `/competitions/{id}/teams/auto-form` | تشكيل تلقائي |
| PATCH | `/competitions/{id}/teams/{teamId}` | تحديث فريق |
| DELETE | `/competitions/{id}/teams/{teamId}` | حذف فريق |

### الهاكاثون
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/competitions/{id}/hackathon` | جلب أيام الهاكاثون |
| POST | `/competitions/{id}/hackathon` | إضافة يوم |
| PATCH | `/competitions/{id}/hackathon/{dayId}` | تحديث يوم |

### المحكمين
| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/competitions/{id}/judges` | جلب المحكمين |
| POST | `/competitions/{id}/judges` | إضافة محكم |
| DELETE | `/competitions/{id}/judges/{judgeId}` | حذف محكم |

### التقييمات
| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/competitions/{id}/evaluations/phase2/{submissionId}` | تقييم المرحلة الثانية |
| POST | `/competitions/{id}/evaluations/team/{teamId}` | تقييم الفريق |
| GET | `/competitions/{id}/evaluations/team/{teamId}` | جلب تقييمات الفريق |

---

## 🔐 الصلاحيات

| الدور | الصلاحيات |
|-------|-----------|
| **Admin** | كل الصلاحيات |
| **Judge** | التقييمات فقط |
| **Participant** | قراءة بياناته ولوحة المتصدرين |

---

## 📝 ملاحظات للمطورين

1. **حساب النقاط التلقائي**: عند تحديث تقدم الفيديو/الاختبار، يتم إعادة حساب نقاط المشارك
2. **تحديث الترتيب**: بعد تغيير النقاط، يتم تحديث ترتيب جميع المشاركين
3. **انتقال المراحل**: عند انتهاء المرحلة، يتم تحديث الحالة تلقائياً
4. **تشكيل الفرق**: الخوارزمية تضمن توازن الفرق حسب التصنيف
5. **الإشعارات**: يجب إرسال إشعارات عند تغيير الحالة أو التأهيل
