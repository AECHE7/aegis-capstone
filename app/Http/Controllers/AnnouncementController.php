<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements for administrative management.
     */
    public function index(\Illuminate\Http\Request $request): View
    {
        $query = Announcement::with('author');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where(function ($q) {
                    $q->whereNull('scheduled_publish_at')->orWhere('scheduled_publish_at', '<=', now());
                })->where(function ($q) {
                    $q->whereNull('scheduled_delete_at')->orWhere('scheduled_delete_at', '>', now());
                });
            } elseif ($status === 'scheduled') {
                $query->where('scheduled_publish_at', '>', now());
            } elseif ($status === 'expired') {
                $query->where('scheduled_delete_at', '<=', now());
            }
        }

        $announcements = $query->latest()->paginate(10, ['*'], 'announcements_page')->withQueryString();

        // Broadcast & Audience Data for Unified Communications
        $scholarships = \App\Models\Scholarship::orderBy('name', 'asc')->get();

        $broadcastQuery = \App\Models\EmailLog::where('subject', 'like', '[A.E.G.I.S. Broadcast]%');

        $bSearch = $request->input('broadcast_search');
        if (empty($bSearch) && ($request->query('tab') === 'history' || $request->routeIs('superadmin.broadcast'))) {
            $bSearch = $request->input('search');
        }

        if (!empty($bSearch)) {
            $bSearch = trim((string) $bSearch);
            $broadcastQuery->where(function ($q) use ($bSearch) {
                $q->where('recipient', 'like', "%{$bSearch}%")
                  ->orWhere('subject', 'like', "%{$bSearch}%")
                  ->orWhere('content', 'like', "%{$bSearch}%");
            });
        }

        $totalBroadcasts = \App\Models\EmailLog::where('subject', 'like', '[A.E.G.I.S. Broadcast]%')->count();
        $uniqueRecipients = \App\Models\EmailLog::where('subject', 'like', '[A.E.G.I.S. Broadcast]%')
            ->distinct('recipient')
            ->count('recipient');

        $broadcasts = $broadcastQuery->latest()->paginate(10, ['*'], 'broadcasts_page')->withQueryString();

        $activeTab = $request->query('tab');
        if (empty($activeTab)) {
            if ($request->has('broadcasts_page') || $request->has('broadcast_search')) {
                $activeTab = 'history';
            } else {
                $activeTab = 'announcements';
            }
        }

        return view('announcements.index', compact(
            'announcements',
            'scholarships',
            'broadcasts',
            'totalBroadcasts',
            'uniqueRecipients',
            'activeTab'
        ));
    }

    /**
     * Display a published listing of announcements for student viewing.
     */
    public function studentFeed(): View
    {
        $announcements = Announcement::with('author')
            ->where(function ($query) {
                $query->whereNull('scheduled_publish_at')
                      ->orWhere('scheduled_publish_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('scheduled_delete_at')
                      ->orWhere('scheduled_delete_at', '>', now());
            })
            ->latest('created_at')
            ->paginate(10);

        return view('student.announcements', compact('announcements'));
    }

    /**
     * Store a newly created announcement and dispatch real-time notifications.
     */
    public function store(StoreAnnouncementRequest $request): JsonResponse
    {
        $announcement = Announcement::create([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'author_id' => auth()->id() ?? 1,
            'scheduled_publish_at' => $request->validated('scheduled_publish_at'),
            'scheduled_delete_at' => $request->validated('scheduled_delete_at'),
        ]);

        // Dispatch notifications to all active portal users (students, staff, director) if published immediately
        $shouldNotify = empty($request->validated('scheduled_publish_at')) || \Carbon\Carbon::parse($request->validated('scheduled_publish_at'))->isPast();
        if ($shouldNotify) {
            try {
                $recipients = User::where('is_active', true)->get();
                Notification::send($recipients, new NewAnnouncementNotification($announcement));

                // Dual-Channel Email Broadcast Dispatch (if requested)
                if ($request->boolean('send_email_broadcast')) {
                    $target = $request->input('broadcast_target', 'all_students');
                    \App\Jobs\BroadcastAnnouncementEmailJob::dispatch($announcement->title, $announcement->content, $target);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to dispatch announcement notifications: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Announcement successfully scheduled!',
            'announcement' => $announcement
        ]);
    }

    /**
     * Update an existing announcement (Edit functionality).
     */
    public function update(UpdateAnnouncementRequest $request, string $id): JsonResponse
    {
        $announcement = Announcement::findOrFail((int) $id);

        $announcement->update([
            'title' => $request->validated('title'),
            'content' => $request->validated('content'),
            'scheduled_publish_at' => $request->validated('scheduled_publish_at'),
            'scheduled_delete_at' => $request->validated('scheduled_delete_at'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully.',
            'announcement' => $announcement->fresh('author'),
        ]);
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $announcement = Announcement::findOrFail((int) $id);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement successfully deleted.'
        ]);
    }

    /**
     * Remove multiple announcements in bulk.
     */
    public function bulkDestroy(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $deleted = Announcement::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deleted} announcements.",
        ]);
    }
}
