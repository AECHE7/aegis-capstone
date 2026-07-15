<?php

namespace App\Enums;

/**
 * ApplicationStatus Enum
 *
 * Replaces raw status strings used throughout controllers.
 * Use ::value when building Eloquent queries.
 *
 * @see MED-02 in AEGIS codebase audit
 */
enum ApplicationStatus: string
{
    case Pending     = 'Pending';
    case UnderReview = 'Under Review';
    case Approved    = 'Approved';
    case Rejected    = 'Rejected';
    case Cancelled   = 'Cancelled';

    /** Human-readable label */
    public function label(): string
    {
        return $this->value;
    }

    /** Bootstrap badge CSS class */
    public function badgeClass(): string
    {
        return match($this) {
            self::Pending     => 'bg-warning text-dark',
            self::UnderReview => 'bg-info text-dark',
            self::Approved    => 'bg-success',
            self::Rejected    => 'bg-danger',
            self::Cancelled   => 'bg-secondary',
        };
    }

    /** Returns true if the application is in a non-terminal state. */
    public function isActive(): bool
    {
        return in_array($this, [self::Pending, self::UnderReview, self::Approved]);
    }

    /**
     * Statuses a student is allowed to cancel.
     * @return array<string>
     */
    public static function cancellable(): array
    {
        return [self::Pending->value, self::UnderReview->value];
    }
}
