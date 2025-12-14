<?php

namespace App\Policies;

use App\Models\User;

class ReportPolicy
{
    /**
     * Determine if the user can export PDF reports
     */
    public function exportPdf(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view reports
     */
    public function viewReports(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can export any format
     */
    public function export(User $user): bool
    {
        return $user->isAdmin();
    }
}
