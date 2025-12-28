<?php

namespace App\Features\SupportTickets;

use App\Features\SupportTickets\Models\SupportTicket;
use App\Features\SupportTickets\Models\SupportTicketReply;
use App\Features\SupportTickets\Policies\SupportTicketPolicy;
use App\Features\SupportTickets\Policies\SupportTicketReplyPolicy;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SupportTicketsFeatureProvider extends ServiceProvider
{
    public $featureName = "SupportTickets";

    public $featureNameLower = "support_tickets";

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mapRoutes();
        $this->loadConfigurations();
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->registerPolicies();
    }

    /**
     * Register policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(SupportTicket::class, SupportTicketPolicy::class);
        Gate::policy(SupportTicketReply::class, SupportTicketReplyPolicy::class);
    }

    /**
     * map routes
     */
    public function mapRoutes(): void
    {
        Route::prefix('api/')
            ->group(__DIR__ . '/Routes/api.php');
    }

    /**
     * Load feature configurations.
     */
    public function loadConfigurations(): void
    {
        $featureConfigFiles = File::files(__DIR__ . '/Config');
        foreach ($featureConfigFiles as $splFile) {
            list($name, $extenssion) = explode('.', $splFile->getFilename());
            $path = $splFile->getRealPath();
            $this->mergeConfigFrom($path, 'features.' . $name);
        }
    }
}
