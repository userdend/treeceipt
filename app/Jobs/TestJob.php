<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        \Log::info('TestJob started');
        sleep(30);
        \Log::info('TestJob finished');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('TestJob started', [
            'job_id' => $this->job?->getJobId(),
            'attempts' => $this->attempts(),
        ]);

        // test failure
        // throw new \Exception('TestJob failed intentionally');

        Log::info('TestJob completed');
    }

    public function failed(Throwable $e): void
    {
        Log::error('TestJob failed', [
            'message' => $e->getMessage(),
            'job_id' => $this->job?->getJobId(),
        ]);
    }
}
