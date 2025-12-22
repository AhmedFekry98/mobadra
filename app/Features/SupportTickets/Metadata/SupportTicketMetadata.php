<?php

namespace App\Features\SupportTickets\Metadata;

use App\Features\SystemManagements\Models\User;

class SupportTicketMetadata
{
    public static function get(): array
    {
        return [
            'priorities' => [
                ['value' => 'low', 'label' => 'Low'],
                ['value' => 'medium', 'label' => 'Medium'],
                ['value' => 'high', 'label' => 'High'],
                ['value' => 'urgent', 'label' => 'Urgent'],
            ],
            'statuses' => [
                ['value' => 'open', 'label' => 'Open'],
                ['value' => 'in_progress', 'label' => 'In Progress'],
                ['value' => 'waiting_reply', 'label' => 'Waiting for Reply'],
                ['value' => 'resolved', 'label' => 'Resolved'],
                ['value' => 'closed', 'label' => 'Closed'],
            ],
            'categories' => [
                ['value' => 'general', 'label' => 'General Inquiry'],
                ['value' => 'technical', 'label' => 'Technical Issue'],
                ['value' => 'billing', 'label' => 'Billing'],
                ['value' => 'account', 'label' => 'Account'],
                ['value' => 'course', 'label' => 'Course Related'],
                ['value' => 'other', 'label' => 'Other'],
            ],
            'staff' => User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->get(['id', 'name', 'email']),
        ];
    }
}
