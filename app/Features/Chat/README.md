# Chat Feature

A real-time chat system supporting private 1-to-1 conversations between teachers and students in the same group.

## Features

- **Private Chat (1-to-1)**: Direct messaging between teacher and student in the same group
- **Real-time**: WebSocket support via Laravel Broadcasting
- **Message Types**: Text, images, files, audio, video
- **Read Receipts**: Track message read status
- **Typing Indicators**: Show when users are typing
- **Attachments**: File uploads with Spatie Media Library

## Chat Rules

- **Teachers** can only start conversations with students in their groups
- **Students** can only start conversations with teachers in their groups
- Only 1-to-1 private conversations between teacher ↔ student
- No group chats or support chats

---

## Database Schema

### Tables

| Table | Description |
|-------|-------------|
| `conversations` | Main conversation records |
| `conversation_participants` | Users in each conversation |
| `messages` | Chat messages |
| `message_reads` | Read receipts |

---

## API Endpoints

### Conversations

```
GET    /api/conversations                    # List user's conversations
POST   /api/conversations                    # Create new conversation
GET    /api/conversations/{id}               # Get conversation details
POST   /api/conversations/{id}/read          # Mark as read
POST   /api/conversations/{id}/mute          # Mute notifications
POST   /api/conversations/{id}/unmute        # Unmute notifications
```

### Messages

```
GET    /api/conversations/{id}/messages      # Get messages (paginated)
POST   /api/conversations/{id}/messages      # Send message
POST   /api/conversations/{id}/messages/read # Mark messages as read
POST   /api/conversations/{id}/typing        # Send typing indicator
PUT    /api/messages/{id}                    # Edit message
DELETE /api/messages/{id}                    # Delete message
```

---

## Usage Examples

### Create Private Conversation

```http
POST /api/conversations
Content-Type: application/json
Authorization: Bearer {token}

{
    "participant_id": 5
}
```

**Response:**
```json
{
    "success": true,
    "message": "Conversation created successfully",
    "data": {
        "id": 1,
        "type": "private",
        "name": "Ahmed Mohamed",
        "participants": [...]
    }
}
```

**Error Response (if not in same group):**
```json
{
    "success": false,
    "message": "You can only start a conversation with teachers/students in your groups"
}
```

### Send Message

```http
POST /api/conversations/1/messages
Content-Type: application/json
Authorization: Bearer {token}

{
    "type": "text",
    "content": "Hello, how are you?"
}
```

### Send Message with Reply

```http
POST /api/conversations/1/messages
Content-Type: application/json
Authorization: Bearer {token}

{
    "type": "text",
    "content": "I agree with you!",
    "reply_to_id": 15
}
```

### Send Message with Attachments

```http
POST /api/conversations/1/messages
Content-Type: multipart/form-data
Authorization: Bearer {token}

type: image
content: Check this out!
attachments[]: (file)
attachments[]: (file)
```

### Get Messages (with pagination)

```http
GET /api/conversations/1/messages?before_id=50
Authorization: Bearer {token}
```

### Edit Message

```http
PUT /api/messages/15
Content-Type: application/json
Authorization: Bearer {token}

{
    "content": "Updated message content"
}
```

### Delete Message

```http
DELETE /api/messages/15
Authorization: Bearer {token}
```

---

## Real-time Events (WebSocket)

### Channel

Private channel: `conversation.{conversationId}`

### Events

#### message.sent
Fired when a new message is sent.

```json
{
    "id": 25,
    "conversation_id": 1,
    "sender_id": 5,
    "sender": {
        "id": 5,
        "name": "Ahmed"
    },
    "type": "text",
    "content": "Hello!",
    "created_at": "2025-01-05T10:30:00.000Z"
}
```

#### message.read
Fired when messages are read.

```json
{
    "conversation_id": 1,
    "user_id": 3,
    "message_ids": [20, 21, 22],
    "read_at": "2025-01-05T10:35:00.000Z"
}
```

#### user.typing
Fired when a user is typing.

```json
{
    "conversation_id": 1,
    "user_id": 5,
    "user_name": "Ahmed",
    "is_typing": true
}
```

### Frontend Integration (Laravel Echo)

```javascript
// Subscribe to conversation channel
Echo.private(`conversation.${conversationId}`)
    .listen('.message.sent', (e) => {
        // Add new message to chat
        messages.push(e);
    })
    .listen('.message.read', (e) => {
        // Update read status
        e.message_ids.forEach(id => {
            markMessageAsRead(id, e.user_id);
        });
    })
    .listen('.user.typing', (e) => {
        // Show typing indicator
        if (e.is_typing) {
            showTypingIndicator(e.user_name);
        } else {
            hideTypingIndicator(e.user_name);
        }
    });
```

---

## Configuration

### Environment Variables

For real-time functionality, configure broadcasting in `.env`:

```env
BROADCAST_DRIVER=pusher
# or
BROADCAST_DRIVER=reverb

PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1
```

### Config File

`app/Features/Chat/Config/chat.php`:

```php
return [
    'max_message_length' => 5000,
    'max_attachments_per_message' => 5,
    'max_attachment_size' => 10240, // 10MB in KB
    'allowed_attachment_types' => ['image/*', 'application/pdf', 'audio/*', 'video/*'],
];
```

---

## File Structure

```
app/Features/Chat/
├── ChatFeatureProvider.php
├── Config/
│   └── chat.php
├── Controllers/
│   ├── ConversationController.php
│   └── MessageController.php
├── Events/
│   ├── MessageSent.php
│   ├── MessageRead.php
│   └── UserTyping.php
├── Migrations/
│   ├── 2025_12_23_0001_create_conversations_table.php
│   ├── 2025_12_23_0002_create_conversation_participants_table.php
│   ├── 2025_12_23_0003_create_messages_table.php
│   └── 2025_12_23_0004_create_message_reads_table.php
├── Models/
│   ├── Conversation.php
│   ├── ConversationParticipant.php
│   ├── Message.php
│   └── MessageRead.php
├── Repositories/
│   ├── ConversationRepository.php
│   └── MessageRepository.php
├── Requests/
│   ├── CreateConversationRequest.php
│   ├── SendMessageRequest.php
│   └── EditMessageRequest.php
├── Routes/
│   └── api.php
├── Services/
│   ├── ConversationService.php
│   └── MessageService.php
├── Transformers/
│   ├── ConversationResource.php
│   ├── ConversationParticipantResource.php
│   └── MessageResource.php
└── README.md
```

---

## Authorization

- Users can only access conversations they are participants in
- Users can only edit/delete their own messages
- Teachers and students must be in the same group to start a conversation

---

## Message Types

| Type | Description |
|------|-------------|
| `text` | Plain text message |
| `image` | Image attachment |
| `file` | File attachment |
| `audio` | Audio message |
| `video` | Video attachment |
| `system` | System-generated message |

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

3. For real-time features, install broadcasting:
```bash
php artisan install:broadcasting
```

---

## Notes

- The feature provider is auto-registered by `FeaturesServiceProvider`
- Messages support soft delete (content is nullified, `is_deleted` flag set)
- Edited messages show `is_edited: true` with `edited_at` timestamp
- Attachments are handled via Spatie Media Library
