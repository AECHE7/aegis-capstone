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
    public function index(): View
    {
        $announcements = Announcement::with('author')->latest()->paginate(10);
        return view('announcements.index', compact('announcements'));
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

        // Dispatch notifications to all student portal users if published immediately
        $shouldNotify = empty($request->validated('scheduled_publish_at')) || \Carbon\Carbon::parse($request->validated('scheduled_publish_at'))->isPast();
        if ($shouldNotify) {
            $students = User::where('role', 'student')->get();
            Notification::send($students, new NewAnnouncementNotification($announcement));
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
}
