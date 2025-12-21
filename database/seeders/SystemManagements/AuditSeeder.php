<?php

namespace Database\Seeders\SystemManagements;

use App\Enums\AuditAction;
use App\Features\SystemManagements\Models\Audit;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding audit records...');

        // Get some users for audit records
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping audit seeding.');
            return;
        }

        // Create sample audit records for different scenarios
        $this->createAuthenticationAudits($users);
        $this->createCrudAudits($users);
        $this->createSecurityAudits($users);
        $this->createBusinessAudits($users);
        $this->createBatchAudits($users);

        $this->command->info('Audit records seeded successfully!');
    }

    /**
     * Create authentication-related audit records
     */
    protected function createAuthenticationAudits($users): void
    {
        $this->command->info('Creating authentication audit records...');

        foreach ($users as $user) {
            // Login audits
            for ($i = 0; $i < rand(5, 15); $i++) {
                Audit::create([
                    'user_id' => $user->id,
                    'action' => AuditAction::LOGIN,
                    'auditable_type' => User::class,
                    'auditable_id' => $user->id,
                    'description' => "User {$user->name} logged in",
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'url' => '/api/auth/login',
                    'method' => 'POST',
                    'metadata' => [
                        'login_method' => 'email_password',
                        'remember_me' => rand(0, 1) === 1,
                    ],
                    'tags' => ['authentication', 'login'],
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }

            // Logout audits
            for ($i = 0; $i < rand(3, 10); $i++) {
                Audit::create([
                    'user_id' => $user->id,
                    'action' => AuditAction::LOGOUT,
                    'auditable_type' => User::class,
                    'auditable_id' => $user->id,
                    'description' => "User {$user->name} logged out",
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'url' => '/api/auth/logout',
                    'method' => 'POST',
                    'tags' => ['authentication', 'logout'],
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }

            // Failed login attempts
            for ($i = 0; $i < rand(1, 5); $i++) {
                Audit::create([
                    'user_id' => null, // Failed login, no user
                    'action' => AuditAction::FAILED_LOGIN,
                    'auditable_type' => User::class,
                    'auditable_id' => $user->id,
                    'description' => "Failed login attempt for {$user->email}",
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'url' => '/api/auth/login',
                    'method' => 'POST',
                    'metadata' => [
                        'email' => $user->email,
                        'reason' => 'invalid_password',
                    ],
                    'tags' => ['authentication', 'failed_login', 'security'],
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }
        }
    }

    /**
     * Create CRUD operation audit records
     */
    protected function createCrudAudits($users): void
    {
        $this->command->info('Creating CRUD audit records...');

        $entityTypes = [
            User::class,
            'App\Features\Badges\Models\Badge',
            'App\Features\Leads\Models\Lead',
            'App\Features\SystemManagements\Models\Role',
        ];

        foreach ($users as $user) {
            for ($i = 0; $i < rand(10, 25); $i++) {
                $entityType = $entityTypes[array_rand($entityTypes)];
                $action = [AuditAction::CREATED, AuditAction::UPDATED, AuditAction::VIEWED][array_rand([0, 1, 2])];

                Audit::create([
                    'user_id' => $user->id,
                    'action' => $action,
                    'auditable_type' => $entityType,
                    'auditable_id' => rand(1, 100),
                    'description' => $this->generateCrudDescription($action, $entityType),
                    'old_values' => $action === AuditAction::UPDATED ? $this->generateOldValues() : null,
                    'new_values' => in_array($action, [AuditAction::CREATED, AuditAction::UPDATED]) ? $this->generateNewValues() : null,
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'url' => $this->generateApiUrl($entityType),
                    'method' => $this->getMethodForAction($action),
                    'tags' => ['crud', strtolower(class_basename($entityType))],
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }
        }
    }

    /**
     * Create security-related audit records
     */
    protected function createSecurityAudits($users): void
    {
        $this->command->info('Creating security audit records...');

        foreach ($users->take(2) as $user) { // Only for admin users
            // Role assignments
            for ($i = 0; $i < rand(2, 5); $i++) {
                Audit::create([
                    'user_id' => $user->id,
                    'action' => AuditAction::ROLE_ASSIGNED,
                    'auditable_type' => User::class,
                    'auditable_id' => $users->random()->id,
                    'description' => "Assigned role to user",
                    'metadata' => [
                        'role_name' => ['Admin', 'Manager', 'Editor'][array_rand(['Admin', 'Manager', 'Editor'])],
                        'assigned_by' => $user->name,
                    ],
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'tags' => ['security', 'role_management'],
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }

            // Permission grants
            for ($i = 0; $i < rand(3, 8); $i++) {
                Audit::create([
                    'user_id' => $user->id,
                    'action' => AuditAction::PERMISSION_GRANTED,
                    'auditable_type' => User::class,
                    'auditable_id' => $users->random()->id,
                    'description' => "Granted permission to user",
                    'metadata' => [
                        'permission' => ['user.create', 'lead.view', 'audit.export'][array_rand(['user.create', 'lead.view', 'audit.export'])],
                        'granted_by' => $user->name,
                    ],
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'tags' => ['security', 'permission_management'],
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }
        }
    }

    /**
     * Create business operation audit records
     */
    protected function createBusinessAudits($users): void
    {
        $this->command->info('Creating business audit records...');

        foreach ($users as $user) {
            // Lead assignments
            for ($i = 0; $i < rand(5, 15); $i++) {
                Audit::create([
                    'user_id' => $user->id,
                    'action' => AuditAction::LEAD_ASSIGNED,
                    'auditable_type' => 'App\Features\Leads\Models\Lead',
                    'auditable_id' => rand(1, 50),
                    'description' => "Lead assigned to user",
                    'metadata' => [
                        'assigned_to' => $users->random()->name,
                        'lead_source' => ['Website', 'Phone', 'Email', 'Referral'][array_rand(['Website', 'Phone', 'Email', 'Referral'])],
                    ],
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'tags' => ['business', 'lead_management'],
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }

            // Payment processing
            for ($i = 0; $i < rand(2, 8); $i++) {
                Audit::create([
                    'user_id' => $user->id,
                    'action' => AuditAction::PAYMENT_PROCESSED,
                    'auditable_type' => 'App\Features\Billing\Models\Payment',
                    'auditable_id' => rand(1, 100),
                    'description' => "Payment processed successfully",
                    'metadata' => [
                        'amount' => rand(100, 5000) / 100,
                        'currency' => 'USD',
                        'payment_method' => ['credit_card', 'paypal', 'bank_transfer'][array_rand(['credit_card', 'paypal', 'bank_transfer'])],
                    ],
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'tags' => ['business', 'payment'],
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                ]);
            }
        }
    }

    /**
     * Create batch operation audit records
     */
    protected function createBatchAudits($users): void
    {
        $this->command->info('Creating batch audit records...');

        foreach ($users->take(2) as $user) {
            $batchId = (string) Str::uuid();
            $batchSize = rand(5, 20);

            for ($i = 0; $i < $batchSize; $i++) {
                Audit::create([
                    'user_id' => $user->id,
                    'action' => AuditAction::BULK_ACTION,
                    'auditable_type' => 'App\Features\Leads\Models\Lead',
                    'auditable_id' => rand(1, 100),
                    'description' => "Bulk operation on leads",
                    'batch_id' => $batchId,
                    'metadata' => [
                        'operation' => 'status_update',
                        'batch_size' => $batchSize,
                        'new_status' => 'processed',
                    ],
                    'ip_address' => $this->randomIpAddress(),
                    'user_agent' => $this->randomUserAgent(),
                    'tags' => ['bulk_operation', 'lead_management'],
                    'created_at' => now()->subDays(rand(1, 15))->subHours(rand(0, 23)),
                ]);
            }
        }
    }

    /**
     * Generate random IP address
     */
    protected function randomIpAddress(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }

    /**
     * Generate random user agent
     */
    protected function randomUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
        ];

        return $userAgents[array_rand($userAgents)];
    }

    /**
     * Generate CRUD description
     */
    protected function generateCrudDescription($action, $entityType): string
    {
        $entityName = class_basename($entityType);
        return match($action) {
            AuditAction::CREATED => "Created {$entityName}",
            AuditAction::UPDATED => "Updated {$entityName}",
            AuditAction::VIEWED => "Viewed {$entityName}",
            AuditAction::DELETED => "Deleted {$entityName}",
            default => "Performed action on {$entityName}",
        };
    }

    /**
     * Generate old values for updates
     */
    protected function generateOldValues(): array
    {
        return [
            'name' => 'Old Name',
            'status' => 'pending',
            'updated_at' => now()->subHours(2)->toISOString(),
        ];
    }

    /**
     * Generate new values
     */
    protected function generateNewValues(): array
    {
        return [
            'name' => 'New Name',
            'status' => 'active',
            'updated_at' => now()->toISOString(),
        ];
    }

    /**
     * Generate API URL for entity type
     */
    protected function generateApiUrl($entityType): string
    {
        $entityName = strtolower(class_basename($entityType));
        return "/api/{$entityName}s/" . rand(1, 100);
    }

    /**
     * Get HTTP method for action
     */
    protected function getMethodForAction($action): string
    {
        return match($action) {
            AuditAction::CREATED => 'POST',
            AuditAction::UPDATED => 'PUT',
            AuditAction::VIEWED => 'GET',
            AuditAction::DELETED => 'DELETE',
            default => 'GET',
        };
    }
}
