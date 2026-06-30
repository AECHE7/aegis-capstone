# SKILL: Publish-Subscribe (Pub/Sub)

## What It Is
Publish-Subscribe is a messaging pattern where **publishers** emit events without knowing who's listening, and **subscribers** register interest in specific events without knowing who published them. This completely decouples the producer of an event from all the things that react to it.

Compare to a direct function call (tightly coupled):
```php
// Tightly coupled: DocumentController directly calls 5 things
$mailer->sendConfirmation($user);
$smsService->notify($user);
$auditLogger->log($document);
$dashboardStats->recompute();
$applicantRecord->updateStatus();
```

With Pub/Sub (loosely coupled):
```php
// Publisher only knows: "something happened"
event(new DocumentAnalyzed($document));
// Each subscriber handles its own concern independently
```

## When to Apply This Concept
- Multiple parts of your system need to react to a single event
- You want to add new reactions without modifying existing code (Open/Closed Principle)
- Real-time UI updates (notify the frontend when analysis completes)
- Audit logging — every important action emits an event that a logger subscribes to
- Cross-cutting concerns: notifications, stats updates, cache invalidation

---

## Laravel Events and Listeners

### 1. Create an Event
```bash
php artisan make:event DocumentAnalyzed
```

```php
// app/Events/DocumentAnalyzed.php
namespace App\Events;

use App\Models\Document;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentAnalyzed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Document $document
    ) {}

    // Which WebSocket channel to broadcast on
    public function broadcastOn(): array
    {
        return [
            new Channel("documents.{$this->document->user_id}"), // public
            // Or: new PrivateChannel("user.{$this->document->user_id}") for private
        ];
    }

    // What data gets sent to the frontend
    public function broadcastWith(): array
    {
        return [
            'document_id'  => $this->document->id,
            'status'       => $this->document->status,
            'fraud_score'  => $this->document->fraud_score,
            'analyzed_at'  => $this->document->analyzed_at->toISOString(),
        ];
    }

    // Name of the event on the frontend
    public function broadcastAs(): string
    {
        return 'document.analyzed';
    }
}
```

### 2. Create Listeners
```bash
php artisan make:listener NotifyApplicantListener --event=DocumentAnalyzed
php artisan make:listener UpdateDashboardStatsListener --event=DocumentAnalyzed
php artisan make:listener AuditLogListener --event=DocumentAnalyzed
```

```php
// app/Listeners/NotifyApplicantListener.php
namespace App\Listeners;

use App\Events\DocumentAnalyzed;
use App\Notifications\DocumentAnalysisComplete;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyApplicantListener implements ShouldQueue  // runs async
{
    public string $queue = 'notifications';
    public int $delay = 0;

    public function handle(DocumentAnalyzed $event): void
    {
        $event->document->user->notify(
            new DocumentAnalysisComplete($event->document)
        );
    }
}
```

```php
// app/Listeners/UpdateDashboardStatsListener.php
class UpdateDashboardStatsListener implements ShouldQueue
{
    public function handle(DocumentAnalyzed $event): void
    {
        Cache::forget('stats.dashboard');
        Cache::forget("stats.docs.user.{$event->document->user_id}");
    }
}
```

```php
// app/Listeners/AuditLogListener.php
class AuditLogListener // synchronous — audit logs should never be skipped
{
    public function handle(DocumentAnalyzed $event): void
    {
        AuditLog::create([
            'user_id'    => $event->document->user_id,
            'action'     => 'document.analyzed',
            'subject_id' => $event->document->id,
            'meta'       => [
                'fraud_score' => $event->document->fraud_score,
                'status'      => $event->document->status,
            ],
        ]);
    }
}
```

### 3. Register in EventServiceProvider
```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    DocumentAnalyzed::class => [
        AuditLogListener::class,           // sync — runs first
        NotifyApplicantListener::class,    // async queue
        UpdateDashboardStatsListener::class, // async queue
    ],

    ApplicationSubmitted::class => [
        AuditLogListener::class,
        NotifyScholarshipOfficerListener::class,
        SendConfirmationEmailListener::class,
    ],

    ScholarshipDeadlineApproaching::class => [
        SendDeadlineReminderListener::class,
    ],
];
```

### 4. Dispatch the Event
```php
// From anywhere: a Job, a Controller, a Service
event(new DocumentAnalyzed($document));

// Or using the helper
DocumentAnalyzed::dispatch($document);
```

---

## AEGIS Event Map

| Event                         | Synchronous Listeners | Async Listeners                          |
|-------------------------------|-----------------------|------------------------------------------|
| `DocumentUploaded`            | `AuditLogListener`    | `AnalyzeDocumentJob` (queue)             |
| `DocumentAnalyzed`            | `AuditLogListener`    | `NotifyApplicantListener`, `UpdateStatsListener` |
| `ApplicationSubmitted`        | `AuditLogListener`    | `NotifyOfficerListener`, `SendConfirmationListener` |
| `ApplicationStatusChanged`    | `AuditLogListener`    | `NotifyApplicantListener`                |
| `ScholarshipCreated`          | —                     | `InvalidateScholarshipCacheListener`     |
| `ScholarshipDeadlineApproaching` | —                  | `SendBatchReminderListener`              |

---

## Real-Time Frontend Updates with Supabase Realtime

Supabase Realtime lets the frontend subscribe to database changes directly, without needing Pusher.

### Backend: When document status updates in DB, Supabase Realtime auto-broadcasts it
```php
// In your Job or Listener — just update the DB record
$document->update(['status' => 'completed', 'fraud_score' => 0.12]);
// Supabase Realtime picks this up automatically if enabled for the table
```

### Frontend: Subscribe to changes (JavaScript)
```javascript
import { createClient } from '@supabase/supabase-js';
const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

// Subscribe to changes on the documents table for the current user
const channel = supabase
  .channel('document-updates')
  .on(
    'postgres_changes',
    {
      event: 'UPDATE',
      schema: 'public',
      table: 'documents',
      filter: `user_id=eq.${currentUserId}`,
    },
    (payload) => {
      console.log('Document updated:', payload.new);
      updateDocumentStatus(payload.new.id, payload.new.status);
      // Show toast notification: "Your document has been analyzed!"
    }
  )
  .subscribe();

// Cleanup on component unmount
return () => supabase.removeChannel(channel);
```

### Enable Realtime on Supabase Table
In Supabase Dashboard → Database → Replication → enable `documents` table.

Or via SQL:
```sql
ALTER PUBLICATION supabase_realtime ADD TABLE documents;
```

---

## Laravel Broadcasting with Reverb (Alternative to Pusher)

Laravel 11+ ships with Reverb (self-hosted WebSocket server). For Laravel 10 on Render, Pusher or Ably work well.

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_HOST=api-mt1.pusherapp.com
PUSHER_PORT=443
PUSHER_SCHEME=https
```

```php
// Frontend (Echo + Pusher)
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: 'mt1',
    forceTLS: true,
});

echo.channel(`documents.${userId}`)
    .listen('.document.analyzed', (e) => {
        console.log('Analysis complete:', e);
        showNotification(`Document analyzed. Fraud score: ${e.fraud_score}`);
    });
```

---

## Scheduled Events (Time-Based Publishing)
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule): void
{
    // Publish a "deadline approaching" event every day at 8 AM
    $schedule->call(function () {
        Scholarship::whereDate('deadline', now()->addDays(3))->each(function ($s) {
            event(new ScholarshipDeadlineApproaching($s));
        });
    })->dailyAt('08:00');
}
```

---

## Anti-Patterns to Avoid
- ❌ Doing critical work (DB writes) only in async listeners — if the queue dies, the work is lost. Use sync listeners for critical operations.
- ❌ Passing full Eloquent models with all relationships in events — serialize only IDs, reload in the listener
- ❌ Having listeners that depend on execution order — if order matters, use chained jobs instead
- ❌ Using events for operations that require a return value — events are fire-and-forget
- ❌ Broadcasting sensitive data (passwords, full documents) over WebSocket channels

---

## Quick Reference
```php
event(new MyEvent($data));           // dispatch
MyEvent::dispatch($data);            // dispatch (static helper)

// In EventServiceProvider $listen array:
MyEvent::class => [
    SyncListener::class,             // runs synchronously
    AsyncListener::class,            // implements ShouldQueue → async
];

// Listener must implement ShouldQueue to run async
class AsyncListener implements ShouldQueue { ... }
```
