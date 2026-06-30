# SKILL: Message Queue

## What It Is
A message queue is a buffer that stores tasks (called "jobs" or "messages") to be processed asynchronously by worker processes. Instead of making a user wait for a slow operation (AI inference, email sending, PDF generation), you push a job to the queue and return an immediate response. A worker process picks up the job in the background and executes it.

## When to Apply This Concept
- Any operation that takes >500ms and doesn't need to return its result to the user immediately
- Sending emails or SMS notifications
- Calling external APIs (Hugging Face inference)
- Generating PDF reports
- Processing uploaded documents (fraud detection)
- Sending webhook notifications
- Batch database operations
- Any task that should retry on failure

---

## Core Architecture

```
HTTP Request
    ↓
Controller → dispatch(new AnalyzeDocumentJob($doc))  ← returns immediately (HTTP 202)
                ↓
            [Redis Queue]  ← job sits here
                ↓
            Queue Worker (separate process)
                ↓
            Hugging Face API call
                ↓
            Store result in DB
                ↓
            Broadcast event to frontend (Supabase Realtime / Pusher)
```

---

## Laravel Queue Setup

### `.env` Configuration
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_password
REDIS_PORT=6379
REDIS_QUEUE_DB=2    # Use DB 2 for queues; 0=default, 1=cache
```

### Create a Job Class
```bash
php artisan make:job AnalyzeDocumentJob
```

```php
// app/Jobs/AnalyzeDocumentJob.php
namespace App\Jobs;

use App\Models\Document;
use App\Services\HuggingFaceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;                  // retry up to 3 times on failure
    public int $timeout = 120;              // kill if running > 2 minutes
    public int $backoff = 30;              // wait 30s before each retry

    public function __construct(
        private readonly Document $document
    ) {}

    public function handle(HuggingFaceService $service): void
    {
        $this->document->update(['status' => 'processing']);

        try {
            $result = $service->analyzeDocument($this->document->file_path);

            $this->document->update([
                'status'           => 'completed',
                'fraud_score'      => $result['fraud_score'],
                'confidence'       => $result['confidence'],
                'analysis_result'  => $result,
                'analyzed_at'      => now(),
            ]);

            // Notify applicant via Supabase Realtime / event
            event(new DocumentAnalyzed($this->document));

        } catch (\Exception $e) {
            $this->document->update(['status' => 'failed']);
            throw $e; // re-throw so Laravel marks job as failed and retries
        }
    }

    public function failed(\Throwable $exception): void
    {
        // Called after all retries exhausted
        $this->document->update(['status' => 'failed']);
        // Notify admin or log alert
        \Log::error("Document analysis permanently failed", [
            'document_id' => $this->document->id,
            'error'       => $exception->getMessage(),
        ]);
    }
}
```

### Dispatching Jobs from a Controller
```php
// app/Http/Controllers/DocumentController.php
use App\Jobs\AnalyzeDocumentJob;

public function upload(Request $request): JsonResponse
{
    $request->validate(['file' => 'required|file|mimes:pdf,jpg,png|max:10240']);

    $path = $request->file('file')->store('documents', 'supabase');

    $document = Document::create([
        'user_id'   => auth()->id(),
        'file_path' => $path,
        'status'    => 'pending',
    ]);

    // Dispatch to queue — returns immediately
    AnalyzeDocumentJob::dispatch($document)
        ->onQueue('ai-analysis')     // named queue for priority
        ->delay(now()->addSeconds(2)); // optional delay

    return response()->json([
        'message'     => 'Document uploaded. Analysis in progress.',
        'document_id' => $document->id,
        'status'      => 'pending',
    ], 202); // 202 Accepted
}
```

### Chaining Jobs (Sequential Pipeline)
```php
// Run jobs one after the other, only if previous succeeded
Bus::chain([
    new ValidateDocumentJob($document),
    new AnalyzeDocumentJob($document),
    new NotifyApplicantJob($document),
])->dispatch();
```

### Batching Jobs (Parallel Execution)
```php
// Process multiple documents in parallel, then run a callback when all done
$batch = Bus::batch([
    new AnalyzeDocumentJob($doc1),
    new AnalyzeDocumentJob($doc2),
    new AnalyzeDocumentJob($doc3),
])->then(function (Batch $batch) {
    // All succeeded
    event(new BatchAnalysisComplete($batch->id));
})->catch(function (Batch $batch, \Throwable $e) {
    // First failure
    \Log::error("Batch analysis failed", ['batch' => $batch->id]);
})->finally(function (Batch $batch) {
    // Runs regardless of success or failure
})->dispatch();

return response()->json(['batch_id' => $batch->id]);
```

---

## Named Queues and Priority

Use different queue names to give priority to certain jobs.

```php
// Dispatch to specific queues
AnalyzeDocumentJob::dispatch($doc)->onQueue('ai-analysis');    // high priority
SendNotificationJob::dispatch($user)->onQueue('notifications'); // medium
GenerateReportJob::dispatch()->onQueue('reports');              // low priority
```

```bash
# Run workers with priority order (processes 'ai-analysis' first)
php artisan queue:work redis \
  --queue=ai-analysis,notifications,reports,default \
  --sleep=3 \
  --tries=3 \
  --timeout=120
```

---

## Failed Jobs
```bash
# Create failed jobs table
php artisan queue:failed-table
php artisan migrate

# View failed jobs
php artisan queue:failed

# Retry a specific failed job
php artisan queue:retry {id}

# Retry all failed jobs
php artisan queue:retry all

# Clear all failed jobs
php artisan queue:flush
```

---

## Running Workers on Render
On Render, queue workers run as a **separate service** (Background Worker).

### `render.yaml` (Render Blueprint)
```yaml
services:
  - type: web
    name: aegis-api
    env: php
    buildCommand: "composer install && php artisan migrate --force"
    startCommand: "php artisan serve --host=0.0.0.0 --port=10000"

  - type: worker
    name: aegis-queue-worker
    env: php
    startCommand: "php artisan queue:work redis --queue=ai-analysis,default --sleep=3 --tries=3 --timeout=120 --max-jobs=1000"
```

### Worker Process Management with Supervisor (if self-hosted)
```ini
; /etc/supervisor/conf.d/aegis-worker.conf
[program:aegis-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2           ; 2 parallel workers
redirect_stderr=true
stdout_logfile=/var/log/aegis-worker.log
stopwaitsecs=120
```

---

## Horizon (Optional: Queue Dashboard)
Laravel Horizon provides a beautiful dashboard to monitor queues and workers.

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

```bash
# Start Horizon (replaces queue:work)
php artisan horizon
```
Access at: `yourapp.com/horizon` (protect with a gate in `HorizonServiceProvider`)

---

## AEGIS Queue Architecture Summary

| Job Class                  | Queue            | Trigger                          | Timeout |
|----------------------------|------------------|----------------------------------|---------|
| `AnalyzeDocumentJob`       | `ai-analysis`    | Document uploaded                | 120s    |
| `SendApplicationStatusJob` | `notifications`  | Application status changed       | 30s     |
| `GenerateReportJob`        | `reports`        | Admin requests export            | 300s    |
| `NotifyDeadlineJob`        | `notifications`  | Scheduled: 3 days before cutoff  | 30s     |

---

## Anti-Patterns to Avoid
- ❌ Doing heavy work synchronously in a controller (makes users wait)
- ❌ Storing large objects (full file content) in job payload — store the path/ID only
- ❌ Not setting `$timeout` — runaway jobs block the worker indefinitely
- ❌ Not having a `failed()` method — silent failures are dangerous
- ❌ Running queue workers without Supervisor/Render worker type (they die and don't restart)
- ❌ Dispatching the same job multiple times without deduplication checks

---

## Quick Reference
```php
JobClass::dispatch($arg);                              // dispatch now
JobClass::dispatch($arg)->delay(now()->addMinutes(5)); // delay
JobClass::dispatch($arg)->onQueue('high');             // named queue
Bus::chain([Job1::dispatch(), Job2::dispatch()])->dispatch(); // chain
Bus::batch([...])->then(fn()=>…)->dispatch();          // batch
php artisan queue:work                                 // start worker
php artisan queue:listen                               // dev (restarts on code change)
```
