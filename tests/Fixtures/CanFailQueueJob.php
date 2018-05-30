<?php

namespace Tests\Fixtures;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CanFailQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shouldThrow;

    /**
     * Class constructor
     *
     * @param boolean $shouldThrow
     */
    public function __construct(bool $shouldThrow = false)
    {
        $this->shouldThrow = $shouldThrow;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->shouldThrow) {
            throw new \Exception;
        }
    }
}
