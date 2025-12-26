# دليل تكامل Zoom مع النظام

## نظرة عامة

هذا الدليل يشرح كيفية إعداد واستخدام Zoom API لإنشاء meetings تلقائياً للـ Group Sessions.

---

## الخطوة 1: إنشاء Server-to-Server OAuth App

1. اذهب إلى [Zoom App Marketplace](https://marketplace.zoom.us/develop/create)
2. اختر **"Server-to-Server OAuth"**
3. أدخل اسم التطبيق (مثال: `Mobadra Meetings`)
4. اضغط **Create**

---

## الخطوة 2: الحصول على Credentials

بعد إنشاء الـ App، ستجد في صفحة **App Credentials**:

| الحقل | الوصف |
|-------|-------|
| **Account ID** | معرّف الحساب |
| **Client ID** | معرّف التطبيق |
| **Client Secret** | المفتاح السري |

---

## الخطوة 3: إضافة Scopes

1. اذهب إلى تبويب **Scopes**
2. اضغط **Add Scopes**
3. أضف الصلاحيات التالية:

| Scope | الوصف |
|-------|-------|
| `meeting:write:admin` | إنشاء وتعديل meetings |
| `meeting:read:admin` | قراءة تفاصيل meetings |
| `user:read:admin` | قراءة بيانات المستخدمين |

4. اضغط **Done**

---

## الخطوة 4: تفعيل التطبيق

1. اذهب إلى تبويب **Activation**
2. اضغط **Activate your app**

---

## الخطوة 5: إعداد الـ .env

أضف الـ credentials في ملف `.env`:

```env
ZOOM_CLIENT_ID=your_client_id_here
ZOOM_CLIENT_SECRET=your_client_secret_here
ZOOM_ACCOUNT_ID=your_account_id_here
```

**مثال:**
```env
ZOOM_CLIENT_ID=1P2G3xfXSjG02CvI_mAGBg
ZOOM_CLIENT_SECRET=86EqMwdekdV3HiQN84zl54rNobfAuDjr
ZOOM_ACCOUNT_ID=AbCdEfGhIjKlMnOp
```

---

## الخطوة 6: مسح الـ Cache

```bash
php artisan config:clear
```

---

## اختبار التكامل

### 1. إنشاء Group Session مع Zoom Meeting

```http
POST /api/group-sessions
Content-Type: application/json
Authorization: Bearer {token}

{
    "group_id": 1,
    "session_date": "2025-01-15",
    "start_time": "14:00",
    "end_time": "15:30",
    "topic": "الحصة الأولى - مقدمة في الرياضيات",
    "meeting_provider": "zoom"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Group session created successfully",
    "data": {
        "id": 1,
        "group_id": 1,
        "session_date": "2025-01-15",
        "start_time": "14:00",
        "end_time": "15:30",
        "topic": "الحصة الأولى - مقدمة في الرياضيات",
        "meeting_provider": "zoom",
        "meeting_id": "123456789",
        "meeting_password": "abc123",
        "has_meeting": true
    }
}
```

### 2. جلب تفاصيل الـ Session (تتضمن روابط الـ Meeting)

```http
GET /api/group-sessions/{sessionId}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "meeting_provider": "zoom",
        "meeting_id": "123456789",
        "has_meeting": true
    }
}
```

---

## روابط الـ Meeting

| الرابط | الاستخدام |
|--------|----------|
| **moderator_link** | رابط المدرس (Host) - يمكنه التحكم في الـ Meeting |
| **attendee_link** | رابط الطلاب (Participants) - للانضمام فقط |

---

## الملفات المتعلقة

```
app/
├── Services/
│   └── ZoomService.php          # Service للتعامل مع Zoom API
├── Features/Groups/
│   └── Services/
│       └── GroupSessionService.php  # يستخدم ZoomService
config/
└── zoom.php                     # إعدادات Zoom
```

---

## ZoomService Methods

| Method | الوصف |
|--------|-------|
| `createMeeting(array $data)` | إنشاء meeting جديد |
| `getMeeting(string $meetingId)` | جلب تفاصيل meeting |
| `updateMeeting(string $meetingId, array $data)` | تحديث meeting |
| `deleteMeeting(string $meetingId)` | حذف meeting |
| `endMeeting(string $meetingId)` | إنهاء meeting جاري |
| `getRecordings(string $meetingId)` | جلب روابط التسجيلات |
| `deleteRecordings(string $meetingId)` | حذف التسجيلات |

---

## التسجيل التلقائي (Cloud Recording)

### الإعداد
التسجيل التلقائي مفعّل بشكل افتراضي (`auto_recording: 'cloud'`).

### إضافة Scopes للتسجيلات
في Zoom App → Scopes، أضف:
- `recording:read:admin`
- `recording:write:admin`

### جلب روابط التسجيل

**ملاحظة مهمة:** التسجيلات تكون متاحة فقط **بعد انتهاء الـ Meeting**.

```php
use App\Services\ZoomService;

$zoom = app(ZoomService::class);
$recordings = $zoom->getRecordings('81489647411');

// النتيجة
[
    'has_recordings' => true,
    'meeting_id' => '81489647411',
    'topic' => 'حصة تجريبية',
    'share_url' => 'https://zoom.us/rec/share/...',
    'recordings' => [
        [
            'id' => 'abc123',
            'type' => 'shared_screen_with_speaker_view',
            'file_type' => 'MP4',
            'play_url' => 'https://zoom.us/rec/play/...',
            'download_url' => 'https://zoom.us/rec/download/...',
            'status' => 'completed',
        ],
        [
            'id' => 'def456',
            'type' => 'audio_only',
            'file_type' => 'M4A',
            'play_url' => 'https://zoom.us/rec/play/...',
            'download_url' => 'https://zoom.us/rec/download/...',
            'status' => 'completed',
        ],
    ],
]
```

### أنواع التسجيلات
| النوع | الوصف |
|-------|-------|
| `shared_screen_with_speaker_view` | فيديو الشاشة مع المتحدث |
| `shared_screen_with_gallery_view` | فيديو الشاشة مع المشاركين |
| `speaker_view` | فيديو المتحدث فقط |
| `gallery_view` | فيديو المشاركين |
| `audio_only` | صوت فقط (M4A) |
| `chat_file` | ملف المحادثة |

### متى تكون التسجيلات متاحة؟
1. **أثناء الـ Meeting:** لا توجد تسجيلات
2. **بعد انتهاء الـ Meeting:** Zoom يعالج التسجيل (قد يستغرق دقائق)
3. **بعد المعالجة:** التسجيلات متاحة للتحميل والمشاهدة

---

## معالجة الأخطاء

### خطأ: Zoom API is not configured
```json
{
    "message": "Zoom API is not configured. Please set ZOOM_CLIENT_ID, ZOOM_CLIENT_SECRET, and ZOOM_ACCOUNT_ID in .env"
}
```
**الحل:** تأكد من إضافة جميع الـ credentials في `.env` وشغّل `php artisan config:clear`

### خطأ: Failed to get Zoom access token
```json
{
    "message": "Failed to get Zoom access token: {error_details}"
}
```
**الحل:** 
- تأكد من صحة الـ credentials
- تأكد من تفعيل الـ App في Zoom
- تأكد من إضافة الـ Scopes المطلوبة

---

## ملاحظات مهمة

1. **الـ Access Token** يتم تخزينه في الـ Cache لمدة ~58 دقيقة (صلاحيته ساعة)
2. **حذف الـ Session** يحذف الـ Zoom Meeting تلقائياً
3. **إذا لم يتم تحديد `meeting_provider`** لن يتم إنشاء Zoom meeting

---

## Troubleshooting

### مسح الـ Zoom Token Cache
```bash
php artisan cache:forget zoom_access_token
```

### التحقق من الإعدادات
```bash
php artisan tinker
>>> config('zoom')
```

يجب أن يظهر:
```php
[
    "client_id" => "your_client_id",
    "client_secret" => "your_client_secret",
    "account_id" => "your_account_id",
    "base_url" => "https://api.zoom.us/v2",
    "oauth_url" => "https://zoom.us/oauth/token",
]
```
