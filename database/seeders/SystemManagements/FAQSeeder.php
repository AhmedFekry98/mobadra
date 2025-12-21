<?php

namespace Database\Seeders\SystemManagements;

use App\Features\SystemManagements\Models\FAQ;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What is ComparthPro and how does it work?',
                'answer' => '<p>ComparthPro is a comprehensive business management platform that helps you streamline your operations, manage leads, track conversations, and handle billing efficiently.</p><p>Our platform offers:</p><ul><li>Lead management and tracking</li><li>Conversation and messaging system</li><li>Automated billing and invoicing</li><li>User role and permission management</li><li>Comprehensive reporting and analytics</li></ul>',
                'sort_order' => 1,
            ],
            [
                'question' => 'How do I get started with ComparthPro?',
                'answer' => '<p>Getting started with ComparthPro is easy:</p><ol><li><strong>Sign up</strong> for an account on our platform</li><li><strong>Complete your profile</strong> with your business information</li><li><strong>Choose a subscription plan</strong> that fits your needs</li><li><strong>Invite team members</strong> and set up user roles</li><li><strong>Start managing</strong> your leads and conversations</li></ol><p>Our onboarding team will guide you through each step to ensure a smooth setup process.</p>',
                'sort_order' => 2,
            ],
            [
                'question' => 'Is my data secure on ComparthPro?',
                'answer' => '<p>Yes, absolutely! Data security is our top priority. We implement industry-standard security measures including:</p><ul><li><strong>SSL encryption</strong> for all data transmission</li><li><strong>Regular security audits</strong> and penetration testing</li><li><strong>GDPR compliance</strong> for data protection</li><li><strong>Role-based access control</strong> to limit data access</li><li><strong>Regular backups</strong> to prevent data loss</li><li><strong>24/7 monitoring</strong> for suspicious activities</li></ul>',
                'sort_order' => 3,
            ],
        ];

        foreach ($faqs as $faq) {
            FAQ::create($faq);
        }
    }
 
}
