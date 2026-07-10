<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Models\User;

class ApplicationAssignmentService
{
    /**
     * Automatically assign an application to an active staff member.
     * Use workload-based load balancing (fewest pending/under-review assignments).
     *
     * @param Application $application
     * @return User|null
     */
    public static function assign(Application $application): ?User
    {
        $scholarshipId = $application->scholarship_id;

        // 1. Find active staff assigned to this specific scholarship program
        $staff = User::where('role', 'admin')
            ->where('is_active', true)
            ->whereHas('scholarships', function ($query) use ($scholarshipId) {
                $query->where('scholarships.id', $scholarshipId);
            })
            ->get();

        // 2. Fall back to all active staff members if none are linked to this scholarship
        if ($staff->isEmpty()) {
            $staff = User::where('role', 'admin')
                ->where('is_active', true)
                ->get();
        }

        // 3. If there are absolutely no active staff members in the system, return null (leave unassigned)
        if ($staff->isEmpty()) {
            return null;
        }

        // 4. Select the staff member with the lowest current workload
        $assignedStaff = $staff->sortBy(function ($user) {
            return $user->assignedApplications()
                ->whereIn('status', ['Pending', 'Under Review'])
                ->count();
        })->first();

        if ($assignedStaff) {
            $application->assigned_to = $assignedStaff->id;
            $application->save();
            return $assignedStaff;
        }

        return null;
    }

    /**
     * Reassign all pending/under-review applications from a deactivated staff member
     * to remaining active staff members.
     *
     * @param User $deactivatedStaff
     * @return int Number of applications reassigned
     */
    public static function reassignPending(User $deactivatedStaff): int
    {
        $applications = Application::where('assigned_to', $deactivatedStaff->id)
            ->whereIn('status', ['Pending', 'Under Review'])
            ->get();

        $count = 0;
        foreach ($applications as $application) {
            // Remove assignment first to prevent sorting count logic from including this staff
            $application->assigned_to = null;
            $application->save();

            $newStaff = self::assign($application);
            if ($newStaff) {
                $count++;
            }
        }

        return $count;
    }
}
