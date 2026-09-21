<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Setting;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        if (app()->environment('testing')) {
            static::created(function (User $user) {
                if ($user->role === 'student' && !isset($user->skip_profile_provision)) {
                    if (!$user->profile()->exists()) {
                        \App\Models\StudentProfile::create([
                            'user_id' => $user->id,
                            'clsu_id_number' => '22-' . rand(1000, 9999),
                            'college' => 'College of Science',
                            'course' => 'BS Information Technology',
                            'year_level' => '3rd Year',
                            'contact_number' => '09171234567',
                            'guardian_name' => 'Guardian Test',
                            'emergency_contact_number' => '09181234567',
                        ]);
                    }
                }
            });
        }
    }

    /**
     * Check if the user has master account privileges.
     */
    public function isMaster(): bool
    {
        $masterEmail = Setting::get('master_email', 'gadianoriel07@gmail.com');
        return $masterEmail && strtolower($this->email) === strtolower($masterEmail);
    }

    /**
     * Dynamically override role attribute for the Master account based on session.
     */
    public function getRoleAttribute($value)
    {
        if (app()->bound('session') && session()->has('active_role') && $this->isMaster()) {
            return session('active_role');
        }
        return $value;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Added role so we can assign Admin/Student
        'email_verified_at',
        'is_active',
        'otp_code',
        'otp_expires_at',
        'has_completed_tour',
        'dpa_consent_at',
    ];

    /**
     * Default model attributes.
     */
    protected $attributes = [
        'has_completed_tour' => false,
        'remember_token' => null,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'otp_expires_at' => 'datetime',
            'has_completed_tour' => 'boolean',
            'dpa_consent_at' => 'datetime',
        ];
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmailNotification());
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }

    // --- NEW: Database Relationships ---

    /**
     * A user can have many applications
     */
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * A user has exactly one student profile
     */
    public function profile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * A user has exactly one invitation (if invited and pending)
     */
    public function invitation()
    {
        return $this->hasOne(UserInvitation::class);
    }

    /**
     * A user (admin/staff) can have many assigned scholarships they handle
     */
    public function scholarships()
    {
        return $this->belongsToMany(Scholarship::class, 'scholarship_staff');
    }

    /**
     * A staff member can have many applications assigned to them for review
     */
    public function assignedApplications()
    {
        return $this->hasMany(Application::class, 'assigned_to');
    }

    /**
     * Check if the student has an active scholarship application or active grant.
     */
    public function hasActiveApplication(): bool
    {
        $activeTerm = \App\Models\AcademicTerm::where('is_active', true)->first();
        $activeTermId = $activeTerm ? $activeTerm->id : null;

        return $this->applications()
            ->where(function ($query) use ($activeTermId) {
                $query->whereIn('status', ['Pending', 'Under Review'])
                      ->orWhere(function ($q) use ($activeTermId) {
                          $q->where('status', 'Approved');
                          if ($activeTermId) {
                              $q->where('academic_term_id', $activeTermId);
                          }
                      });
            })
            ->exists();
    }

    /**
     * A user can have many remembered MFA devices.
     */
    public function mfaDevices()
    {
        return $this->hasMany(UserMfaDevice::class);
    }

    /**
     * Check if the student user has completed their basic profile information.
     */
    public function isProfileComplete(): bool
    {
        if ($this->role !== 'student') {
            return true;
        }

        $profile = $this->profile;
        if (!$profile) {
            return false;
        }

        return !empty($profile->clsu_id_number)
            && !empty($profile->college)
            && !empty($profile->course)
            && !empty($profile->year_level)
            && !empty($profile->contact_number)
            && !empty($profile->emergency_contact_number);
    }
}