# Courses Feature - Lesson Content API

## Overview

The Lesson Content system uses polymorphic relationships to support different content types within lessons. Each lesson can have multiple content items of various types.

## Content Types

| Type | Description | Table |
|------|-------------|-------|
| `video` | Video content (YouTube, Vimeo, local) | `video_contents` |
| `live_session` | Live meeting sessions (Zoom, Google Meet, Teams) | `live_sessions` |
| `quiz` | Quizzes with configurable settings | `quizzes` |
| `assignment` | Assignments with due dates and file uploads | `assignments` |
| `material` | Downloadable materials (PDF, documents) | `materials` |

---

## API Endpoints

### Base URL: `/api/lesson-contents`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | List all lesson contents |
| POST | `/` | Create new lesson content |
| GET | `/{id}` | Get single lesson content |
| PUT | `/{id}` | Update lesson content |
| DELETE | `/{id}` | Delete lesson content |
| GET | `/metadata` | Get metadata for filtering |
| GET | `/lesson/{lessonId}` | Get contents by lesson |

---

## Create Lesson Content

### Request Structure

```json
{
    "lesson_id": 1,
    "content_type": "video|live_session|quiz|assignment|material",
    "title": "Content Title",
    "description": "Optional description",
    "order": 1,
    "duration": 0,
    "is_required": true,
    "is_published": true,
    "content_data": {
        // Type-specific fields (see below)
    }
}
```

---

## Content Type Examples

### 1. Video Content

```json
POST /api/lesson-contents
{
    "lesson_id": 1,
    "content_type": "video",
    "title": "Introduction to Laravel",
    "description": "Learn the basics of Laravel framework",
    "order": 1,
    "is_required": true,
    "is_published": true,
    "content_data": {
        "video_url": "https://www.youtube.com/watch?v=example",
        "video_provider": "youtube",
        "duration": 1800,
        "thumbnail_url": "https://example.com/thumbnail.jpg"
    }
}
```

**Content Data Fields:**

| Field | Type | Required | Default | Description |
|-------|------|----------|---------|-------------|
| `video_url` | string | Yes | - | URL of the video |
| `video_provider` | string | No | `youtube` | Provider: `youtube`, `vimeo`, `local` |
| `duration` | integer | No | `0` | Duration in seconds |
| `thumbnail_url` | string | No | `null` | Thumbnail image URL |

---

### 2. Live Session

```json
POST /api/lesson-contents
{
    "lesson_id": 1,
    "content_type": "live_session",
    "title": "Weekly Q&A Session",
    "description": "Live session with the instructor",
    "order": 2,
    "is_required": false,
    "is_published": true,
    "content_data": {
        "meeting_url": "https://zoom.us/j/1234567890",
        "meeting_provider": "zoom",
        "start_time": "2025-01-15 10:00:00",
        "end_time": "2025-01-15 11:30:00",
        "max_participants": 100
    }
}
```

**Content Data Fields:**

| Field | Type | Required | Default | Description |
|-------|------|----------|---------|-------------|
| `meeting_url` | string | No | `null` | Meeting URL |
| `meeting_provider` | string | No | `zoom` | Provider: `zoom`, `google_meet`, `teams` |
| `start_time` | datetime | Yes | - | Session start time |
| `end_time` | datetime | No | `null` | Session end time |
| `max_participants` | integer | No | `null` | Maximum participants allowed |

---

### 3. Quiz

```json
POST /api/lesson-contents
{
    "lesson_id": 1,
    "content_type": "quiz",
    "title": "Chapter 1 Assessment",
    "description": "Test your knowledge of Chapter 1",
    "order": 3,
    "is_required": true,
    "is_published": true,
    "content_data": {
        "time_limit": 30,
        "passing_score": 70,
        "max_attempts": 3,
        "shuffle_questions": true,
        "show_answers": false
    }
}
```

**Content Data Fields:**

| Field | Type | Required | Default | Description |
|-------|------|----------|---------|-------------|
| `time_limit` | integer | No | `null` | Time limit in minutes |
| `passing_score` | integer | No | `60` | Passing score percentage (0-100) |
| `max_attempts` | integer | No | `1` | Maximum attempts allowed |
| `shuffle_questions` | boolean | No | `false` | Randomize question order |
| `show_answers` | boolean | No | `false` | Show correct answers after submission |

---

### 4. Assignment

```json
POST /api/lesson-contents
{
    "lesson_id": 1,
    "content_type": "assignment",
    "title": "Week 1 Project",
    "description": "Build a simple REST API",
    "order": 4,
    "is_required": true,
    "is_published": true,
    "content_data": {
        "instructions": "Create a REST API with CRUD operations for a blog system.",
        "due_date": "2025-01-20 23:59:59",
        "max_score": 100,
        "allow_late_submission": true,
        "allowed_file_types": ["pdf", "zip", "doc", "docx"],
        "max_file_size": 10
    }
}
```

**Content Data Fields:**

| Field | Type | Required | Default | Description |
|-------|------|----------|---------|-------------|
| `instructions` | text | No | `null` | Assignment instructions |
| `due_date` | datetime | No | `null` | Submission deadline |
| `max_score` | integer | No | `100` | Maximum score |
| `allow_late_submission` | boolean | No | `false` | Allow late submissions |
| `allowed_file_types` | array | No | `null` | Allowed file extensions |
| `max_file_size` | integer | No | `null` | Max file size in MB |

---

### 5. Material

```json
POST /api/lesson-contents
{
    "lesson_id": 1,
    "content_type": "material",
    "title": "Course Slides - Week 1",
    "description": "Presentation slides for week 1",
    "order": 5,
    "is_required": false,
    "is_published": true,
    "content_data": {
        "file_url": "https://example.com/files/week1-slides.pdf",
        "file_type": "pdf",
        "file_size": 2048000,
        "is_downloadable": true
    }
}
```

**Content Data Fields:**

| Field | Type | Required | Default | Description |
|-------|------|----------|---------|-------------|
| `file_url` | string | Yes | - | URL of the file |
| `file_type` | string | Yes | - | File type: `pdf`, `doc`, `ppt`, etc. |
| `file_size` | integer | No | `null` | File size in bytes |
| `is_downloadable` | boolean | No | `true` | Allow file download |

---

## Update Lesson Content

```json
PUT /api/lesson-contents/{id}
{
    "title": "Updated Title",
    "is_published": false,
    "content_data": {
        "video_url": "https://youtube.com/watch?v=new-video"
    }
}
```

Only include fields you want to update. The `content_data` will update the polymorphic content.

---

## Response Structure

### Success Response

```json
{
    "status": true,
    "message": "Lesson content created successfully",
    "data": {
        "id": 1,
        "lesson_id": 1,
        "content_type": "video",
        "contentable_type": "App\\Features\\Courses\\Models\\VideoContent",
        "contentable_id": 1,
        "contentable": {
            "id": 1,
            "video_url": "https://youtube.com/watch?v=example",
            "video_provider": "youtube",
            "duration": 1800,
            "thumbnail_url": "https://example.com/thumbnail.jpg"
        },
        "title": "Introduction to Laravel",
        "description": "Learn the basics",
        "order": 1,
        "duration": 0,
        "is_required": true,
        "is_published": true,
        "created_at": "2025-01-01 10:00:00",
        "updated_at": "2025-01-01 10:00:00"
    }
}
```

---

## Database Schema

### lesson_contents

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| lesson_id | bigint | Foreign key to lessons |
| content_type | string | Type identifier |
| contentable_type | string | Polymorphic model class |
| contentable_id | bigint | Polymorphic model ID |
| title | string | Content title |
| description | text | Content description |
| order | integer | Display order |
| duration | integer | Duration in seconds |
| is_required | boolean | Required to complete |
| is_published | boolean | Published status |

### Polymorphic Tables

- `video_contents`
- `live_sessions`
- `quizzes`
- `assignments`
- `materials`

---

## Permissions

| Permission | Description |
|------------|-------------|
| `lesson_content.viewAny` | View all lesson contents |
| `lesson_content.view` | View single lesson content |
| `lesson_content.create` | Create lesson content |
| `lesson_content.update` | Update lesson content |
| `lesson_content.delete` | Delete lesson content |
