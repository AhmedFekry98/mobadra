<?php

namespace App\Enums;

enum AuditAction: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';
    case VIEWED = 'viewed';
    case DOWNLOADED = 'downloaded';
    case UPLOADED = 'uploaded';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case PASSWORD_CHANGED = 'password_changed';
    case EMAIL_CHANGED = 'email_changed';
    case ROLE_ASSIGNED = 'role_assigned';
    case ROLE_REMOVED = 'role_removed';
    case PERMISSION_GRANTED = 'permission_granted';
    case PERMISSION_REVOKED = 'permission_revoked';
    case EXPORTED = 'exported';
    case IMPORTED = 'imported';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case SUSPENDED = 'suspended';
    case ACTIVATED = 'activated';
    case DEACTIVATED = 'deactivated';
    case PAYMENT_PROCESSED = 'payment_processed';
    case SUBSCRIPTION_CREATED = 'subscription_created';
    case SUBSCRIPTION_CANCELLED = 'subscription_cancelled';
    case LEAD_ASSIGNED = 'lead_assigned';
    case LEAD_STATUS_CHANGED = 'lead_status_changed';
    case CONVERSATION_STARTED = 'conversation_started';
    case MESSAGE_SENT = 'message_sent';
    case FILE_ATTACHED = 'file_attached';
    case SETTINGS_UPDATED = 'settings_updated';
    case BULK_ACTION = 'bulk_action';
    case API_ACCESS = 'api_access';
    case FAILED_LOGIN = 'failed_login';
    case SECURITY_ALERT = 'security_alert';

    /**
     * Get all action values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get actions by category
     */
    public static function getCrudActions(): array
    {
        return [
            self::CREATED->value,
            self::UPDATED->value,
            self::DELETED->value,
            self::RESTORED->value,
        ];
    }

    public static function getAuthActions(): array
    {
        return [
            self::LOGIN->value,
            self::LOGOUT->value,
            self::PASSWORD_CHANGED->value,
            self::EMAIL_CHANGED->value,
            self::FAILED_LOGIN->value,
        ];
    }

    public static function getSecurityActions(): array
    {
        return [
            self::ROLE_ASSIGNED->value,
            self::ROLE_REMOVED->value,
            self::PERMISSION_GRANTED->value,
            self::PERMISSION_REVOKED->value,
            self::SECURITY_ALERT->value,
        ];
    }

    public static function getBusinessActions(): array
    {
        return [
            self::PAYMENT_PROCESSED->value,
            self::SUBSCRIPTION_CREATED->value,
            self::SUBSCRIPTION_CANCELLED->value,
            self::LEAD_ASSIGNED->value,
            self::LEAD_STATUS_CHANGED->value,
        ];
    }

    /**
     * Get human readable label
     */
    public function label(): string
    {
        return match($this) {
            self::CREATED => 'Created',
            self::UPDATED => 'Updated',
            self::DELETED => 'Deleted',
            self::RESTORED => 'Restored',
            self::VIEWED => 'Viewed',
            self::DOWNLOADED => 'Downloaded',
            self::UPLOADED => 'Uploaded',
            self::LOGIN => 'Login',
            self::LOGOUT => 'Logout',
            self::PASSWORD_CHANGED => 'Password Changed',
            self::EMAIL_CHANGED => 'Email Changed',
            self::ROLE_ASSIGNED => 'Role Assigned',
            self::ROLE_REMOVED => 'Role Removed',
            self::PERMISSION_GRANTED => 'Permission Granted',
            self::PERMISSION_REVOKED => 'Permission Revoked',
            self::EXPORTED => 'Exported',
            self::IMPORTED => 'Imported',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::SUSPENDED => 'Suspended',
            self::ACTIVATED => 'Activated',
            self::DEACTIVATED => 'Deactivated',
            self::PAYMENT_PROCESSED => 'Payment Processed',
            self::SUBSCRIPTION_CREATED => 'Subscription Created',
            self::SUBSCRIPTION_CANCELLED => 'Subscription Cancelled',
            self::LEAD_ASSIGNED => 'Lead Assigned',
            self::LEAD_STATUS_CHANGED => 'Lead Status Changed',
            self::CONVERSATION_STARTED => 'Conversation Started',
            self::MESSAGE_SENT => 'Message Sent',
            self::FILE_ATTACHED => 'File Attached',
            self::SETTINGS_UPDATED => 'Settings Updated',
            self::BULK_ACTION => 'Bulk Action',
            self::API_ACCESS => 'API Access',
            self::FAILED_LOGIN => 'Failed Login',
            self::SECURITY_ALERT => 'Security Alert',
        };
    }

    /**
     * Check if action is sensitive (requires special attention)
     */
    public function isSensitive(): bool
    {
        return in_array($this, [
            self::DELETED,
            self::ROLE_ASSIGNED,
            self::ROLE_REMOVED,
            self::PERMISSION_GRANTED,
            self::PERMISSION_REVOKED,
            self::PASSWORD_CHANGED,
            self::FAILED_LOGIN,
            self::SECURITY_ALERT,
            self::SUSPENDED,
            self::DEACTIVATED,
        ]);
    }

    /**
     * Get color for UI representation
     */
    public function color(): string
    {
        return match($this) {
            self::CREATED, self::ACTIVATED, self::APPROVED => 'green',
            self::UPDATED, self::VIEWED, self::LOGIN => 'blue',
            self::DELETED, self::SUSPENDED, self::REJECTED, self::FAILED_LOGIN => 'red',
            self::RESTORED, self::LOGOUT => 'yellow',
            self::SECURITY_ALERT => 'orange',
            default => 'gray',
        };
    }
}
