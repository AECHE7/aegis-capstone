<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class WakeAICommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aegis:wake-ai {--timeout=30 : Timeout in seconds for each wake attempt} {--retries=3 : Maximum wake retries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wake up sleeping AEGIS AI microservice container and verify health status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $aiUrl = rtrim(config('services.ai.url', 'http://127.0.0.1:5000'), '/');
        $timeout = (int) $this->option('timeout');
        $maxRetries = (int) $this->option('retries');

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("   A.E.G.I.S. AI Microservice Auto-Wake Utility");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("Target URL: <comment>{$aiUrl}</comment>");

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $this->line("Attempt {$attempt}/{$maxRetries}: Pinging AI health endpoint...");
            $startTime = microtime(true);

            try {
                $response = Http::timeout($timeout)->get($aiUrl . '/health');
                $elapsed = round((microtime(true) - $startTime) * 1000);

                if ($response->successful()) {
                    $this->newLine();
                    $this->info(" [ONLINE] AI Microservice is alive and fully warmed up! ({$elapsed}ms)");
                    
                    $data = $response->json();
                    if (is_array($data)) {
                        $this->table(
                            ['Property', 'Value'],
                            collect($data)->map(fn ($val, $key) => [
                                $key,
                                is_bool($val) ? ($val ? 'true' : 'false') : (is_array($val) ? json_encode($val) : (string) $val)
                            ])->toArray()
                        );
                    }
                    return self::SUCCESS;
                }

                $this->warn("AI returned HTTP {$response->status()} (Service may be waking up from sleep)...");
            } catch (\Exception $e) {
                $this->warn("Connection attempt {$attempt} failed: " . $e->getMessage());
            }

            if ($attempt < $maxRetries) {
                $this->line("Waiting 10 seconds for container initialization...");
                sleep(10);
            }
        }

        $this->error("Failed to wake AI microservice after {$maxRetries} attempts.");
        return self::FAILURE;
    }
}
