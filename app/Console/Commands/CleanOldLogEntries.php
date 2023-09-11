<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;

class CleanOldLogEntries extends Command
{
    protected $signature = 'log:clean';
    protected $description = 'Remove log entries older than 1 week from worker.log';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $logFile = storage_path('logs/worker.log​');

        // Check if file exists
        if (!file_exists($logFile)) {
            $this->error("Log file {$logFile} not found.");
            return;
        }

        // Get the current date
        $now = new DateTime();

        // Read the file line-by-line
        $lines = file($logFile);
        $newContent = '';

        foreach ($lines as $line) {
            // Match the date using regex
            preg_match("/\[(.*?)\]/", $line, $matches);
            
            if (isset($matches[1])) {
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $matches[1]);
                
                if ($date) { // Ensure the date was parsed successfully
                    // Check if the log's date is not older than 1 week
                    $interval = $now->diff($date);
                    $daysOld = (int) $interval->format('%a');

                    if ($daysOld <= 3) {
                        $newContent .= $line;
                    }
                }
            }
        }

        // Save the new content to the log file
        file_put_contents($logFile, $newContent);
        $this->info('Old log entries have been removed.');
    }
}
