<?php

namespace App\Providers;

use App\Models\TaskSubmission;
use App\Policies\TaskSubmissionPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(TaskSubmission::class, TaskSubmissionPolicy::class);

        // Register report gates
        Gate::define('export-pdf', [ReportPolicy::class, 'exportPdf']);
        Gate::define('view-reports', [ReportPolicy::class, 'viewReports']);
        Gate::define('export-reports', [ReportPolicy::class, 'export']);
    }
}
