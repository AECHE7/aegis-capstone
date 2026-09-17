<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\Setting;

class OfficeHoursService
{
    public const TIMEZONE = 'Asia/Manila';

    /**
     * Check if the current (or given) time is within official CLSU OSA office hours.
     */
    public static function isWithinOfficeHours(?Carbon $time = null): bool
    {
        $now = $time ? $time->copy()->setTimezone(self::TIMEZONE) : Carbon::now(self::TIMEZONE);

        // Retrieve configured hours or default to 08:00 - 17:00 (8 AM - 5 PM)
        $startHour = (int) Setting::get('office_hours_start', '8');
        $endHour = (int) Setting::get('office_hours_end', '17');

        // Monday (1) to Friday (5)
        if ($now->isWeekend()) {
            return false;
        }

        $currentTimeVal = (int) $now->format('Hi'); // e.g. 0830, 1700
        $startTimeVal = $startHour * 100;
        $endTimeVal = $endHour * 100;

        return $currentTimeVal >= $startTimeVal && $currentTimeVal < $endTimeVal;
    }

    /**
     * Alias for isWithinOfficeHours.
     */
    public static function isOfficeHours(?Carbon $time = null): bool
    {
        return self::isWithinOfficeHours($time);
    }

    /**
     * Check if the current time is outside official office hours.
     */
    public static function isOutsideOfficeHours(?Carbon $time = null): bool
    {
        return !self::isWithinOfficeHours($time);
    }

    /**
     * Get the next opening business window time.
     */
    public static function getNextOpeningTime(?Carbon $time = null): Carbon
    {
        $now = $time ? $time->copy()->setTimezone(self::TIMEZONE) : Carbon::now(self::TIMEZONE);
        $startHour = (int) Setting::get('office_hours_start', '8');

        $candidate = $now->copy();

        if ($now->isWeekend()) {
            $candidate->next(Carbon::MONDAY)->setTime($startHour, 0, 0);
        } elseif ((int) $now->format('H') >= (int) Setting::get('office_hours_end', '17')) {
            $candidate->addDay();
            if ($candidate->isWeekend()) {
                $candidate->next(Carbon::MONDAY);
            }
            $candidate->setTime($startHour, 0, 0);
        } elseif ((int) $now->format('H') < $startHour) {
            $candidate->setTime($startHour, 0, 0);
        }

        return $candidate;
    }

    /**
     * Human-readable schedule description.
     */
    public static function getScheduleDescription(): string
    {
        return 'Monday to Friday, 8:00 AM – 5:00 PM PHT';
    }
}
