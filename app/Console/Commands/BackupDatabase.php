<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Artisan command: pharmacy:backup
 *
 * Scheduled daily at 02:00 (configured in bootstrap/app.php withSchedule).
 * Dumps the MySQL database to a gzip-compressed file in storage/app/backups/,
 * then prunes files older than PHARMACY_BACKUP_RETENTION_DAYS (default: 30).
 *
 * Requirements on the server: mysqldump and gzip must be in $PATH.
 */
class BackupDatabase extends Command
{
    protected $signature = 'pharmacy:backup
                            {--no-prune : Skip pruning old backups}
                            {--retention= : Override retention days (default from config)}';

    protected $description = 'Dump the MySQL database to a gzip-compressed file and prune old backups.';

    public function handle(): int
    {
        $this->info('Starting database backup…');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', 3306);

        if (empty($database)) {
            $this->error('No MySQL database configured. Check DB_DATABASE in .env');
            return self::FAILURE;
        }

        // Build destination path
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "backup-{$database}-{$timestamp}.sql.gz";
        $directory = storage_path('app/backups');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fullPath = "{$directory}/{$filename}";

        // Build the mysqldump command (no plaintext password in $argv)
        $dumpCmd = array_filter([
            'mysqldump',
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            $password !== null && $password !== '' ? "--password={$password}" : null,
            '--single-transaction',     // InnoDB-safe: consistent snapshot without table lock
            '--quick',                   // Stream rows rather than buffering entire table in RAM
            '--routines',                // Include stored procedures/functions if any
            '--skip-lock-tables',
            $database,
        ]);

        $gzipCmd = ['gzip', '--stdout', '-9'];

        $this->info("Dumping database `{$database}` to {$filename}…");

        try {
            // Pipe: mysqldump | gzip -9 > file
            $dump = new Process(array_values($dumpCmd));
            $dump->setTimeout(600); // 10 minutes max for large DBs
            $dump->start();

            $outputHandle = fopen($fullPath, 'wb');
            if ($outputHandle === false) {
                throw new \RuntimeException("Cannot open output file: {$fullPath}");
            }

            $gzip = new Process($gzipCmd, null, null, $dump->getIterator());
            $gzip->setTimeout(600);
            $gzip->run(function ($type, $data) use ($outputHandle): void {
                if ($type === Process::OUT) {
                    fwrite($outputHandle, $data);
                }
            });

            $dump->wait();
            fclose($outputHandle);

            if (!$dump->isSuccessful()) {
                throw new ProcessFailedException($dump);
            }

            $sizeKb = round(filesize($fullPath) / 1024, 1);
            $this->info("Backup complete: {$filename} ({$sizeKb} KB)");
            Log::channel('daily')->info("Database backup succeeded: {$filename} ({$sizeKb} KB)");

        } catch (\Throwable $e) {
            // Clean up a partial file if something went wrong
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
            $this->error("Backup failed: {$e->getMessage()}");
            Log::channel('daily')->error('Database backup failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            return self::FAILURE;
        }

        // ── Prune old backups ─────────────────────────────────────────────
        if (!$this->option('no-prune')) {
            $retentionDays = (int) ($this->option('retention') ?? config('pharmacy.backup_retention_days', 30));
            $this->pruneOldBackups($directory, $retentionDays);
        }

        return self::SUCCESS;
    }

    private function pruneOldBackups(string $directory, int $retentionDays): void
    {
        $cutoff = now()->subDays($retentionDays)->getTimestamp();
        $pruned = 0;

        foreach (glob("{$directory}/backup-*.sql.gz") as $file) {
            if (filemtime($file) < $cutoff) {
                @unlink($file);
                $pruned++;
                $this->line("  Pruned old backup: " . basename($file));
            }
        }

        if ($pruned > 0) {
            $this->info("Pruned {$pruned} backup(s) older than {$retentionDays} days.");
            Log::channel('daily')->info("Backup pruner removed {$pruned} old file(s).");
        } else {
            $this->info("No old backups to prune.");
        }
    }
}
