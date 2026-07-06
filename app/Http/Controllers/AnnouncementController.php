<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements for administrative management.
     */
    public function index()
    {
        $announcements = Announcement::with('author')->latest()->paginate(10);
        return view('announcements.index', compact('announcements'));
    }

    /**
     * Store a newly created announcement and dispatch real-time notifications.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'author_id' => auth()->id() ?? 1,
        ]);

        // Dispatch notifications to all student portal users
        $students = User::where('role', 'student')->get();
        Notification::send($students, new NewAnnouncementNotification($announcement));

        return response()->json([
            'success' => true,
            'message' => 'Announcement published successfully!',
            'announcement' => $announcement
        ]);
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement successfully deleted.'
        ]);
    }
}
