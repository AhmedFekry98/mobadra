# Community Feature

A social community feature for creating posts, likes, comments, and reply comments.

## Features

- **Posts**: Create, edit, delete posts with attachments
- **Likes**: Like/unlike posts and comments
- **Comments**: Comment on posts
- **Replies**: Reply to comments (nested comments)

---

## Database Schema

### Tables

| Table | Description |
|-------|-------------|
| `posts` | User posts with content and media |
| `post_likes` | Post likes by users |
| `comments` | Comments on posts (supports nesting) |
| `comment_likes` | Comment likes by users |

---

## API Endpoints

### Posts

```
GET    /api/community/posts                    # List all posts
POST   /api/community/posts                    # Create new post
GET    /api/community/posts/{id}               # Get post with comments
PUT    /api/community/posts/{id}               # Update post
DELETE /api/community/posts/{id}               # Delete post
POST   /api/community/posts/{id}/like          # Toggle like on post
```

### Comments

```
GET    /api/community/posts/{postId}/comments  # Get post comments
POST   /api/community/posts/{postId}/comments  # Create comment (or reply)
```

---

## Usage Examples

### Create Post

```http
POST /api/community/posts
Content-Type: multipart/form-data
Authorization: Bearer {token}

content: This is my first post!
visibility: public
attachments[]: (file)
```

**Response:**
```json
{
    "success": true,
    "message": "Post created successfully",
    "data": {
        "id": 1,
        "content": "This is my first post!",
        "visibility": "public",
        "likes_count": 0,
        "comments_count": 0,
        "is_liked": false,
        "user": {
            "id": 5,
            "name": "Ahmed",
            "avatar": "..."
        },
        "attachments": [
            {
                "id": 1,
                "url": "http://localhost/storage/media/...",
                "name": "image.jpg",
                "mime_type": "image/jpeg",
                "size": 102400,
                "extension": "jpg"
            }
        ],
        "created_at": "2025-01-05T10:30:00.000Z",
        "created_at_human": "just now"
    }
}
```

### Like Post

```http
POST /api/community/posts/1/like
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Post liked",
    "data": {
        "is_liked": true,
        "likes_count": 15
    }
}
```

### Create Comment

```http
POST /api/community/posts/1/comments
Content-Type: application/json
Authorization: Bearer {token}

{
    "content": "Great post!"
}
```

### Reply to Comment

```http
POST /api/community/posts/1/comments
Content-Type: application/json
Authorization: Bearer {token}

{
    "content": "I agree with you!",
    "parent_id": 5
}
```

---

## File Structure

```
app/Features/Community/
├── CommunityFeatureProvider.php
├── Controllers/
│   ├── PostController.php
│   └── CommentController.php
├── Migrations/
│   ├── 2025_12_23_0001_create_posts_table.php
│   ├── 2025_12_23_0002_create_post_likes_table.php
│   ├── 2025_12_23_0003_create_comments_table.php
│   └── 2025_12_23_0004_create_comment_likes_table.php
├── Models/
│   ├── Post.php
│   ├── PostLike.php
│   ├── Comment.php
│   └── CommentLike.php
├── Repositories/
│   ├── PostRepository.php
│   └── CommentRepository.php
├── Requests/
│   ├── CreatePostRequest.php
│   ├── UpdatePostRequest.php
│   ├── CreateCommentRequest.php
│   └── UpdateCommentRequest.php
├── Routes/
│   └── api.php
├── Services/
│   ├── PostService.php
│   └── CommentService.php
├── Transformers/
│   ├── PostResource.php
│   └── CommentResource.php
└── README.md
```

---

## Authorization

- Users can only edit/delete their own posts
- Users can only edit/delete their own comments

---

## Media

- Posts support attachments (max 10MB per file)
- Media is stored in `storage/app/public/media`

---

## Installation

1. Run migrations:
```bash
php artisan migrate
```

2. Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```
