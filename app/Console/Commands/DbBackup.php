<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DbBackup extends Command
{
    protected $signature = 'db:backup {--keep=14 : Keep last N backups}';
    protected $description = 'Create a MySQL backup as plain .sql file (no password, no gzip)';

    public function handle(): int
    {
        $db = config('database.connections.mysql');

        $host = $db['host'] ?? 'localhost';
        $port = $db['port'] ?? 3306;
        $database = $db['database'] ?? null;
        $username = $db['username'] ?? null;

        if (!$database || !$username) {
            $this->error('Database config missing. Check .env');
            return self::FAILURE;
        }

        // Backup directory
        $backupDir = base_path(env('DB_BACKUP_PATH', 'storage/app/backups'));
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // File name
        $timestamp = now()->format('Y-m-d_H-i-s');
        $fileName = "{$database}_{$timestamp}.sql";
        $fullPath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

        // mysqldump path
        $mysqldump = env('MYSQLDUMP_BIN', 'mysqldump');

        // Escape args
        $hostArg = escapeshellarg($host);
        $portArg = escapeshellarg((string) $port);
        $dbArg   = escapeshellarg($database);
        $userArg = escapeshellarg($username);
        $outArg  = escapeshellarg($fullPath);

        // Command (NO password at all)
        $cmd =
            escapeshellarg($mysqldump) .
            " --single-transaction --quick --routines --triggers" .
            " -h {$hostArg} -P {$portArg} -u {$userArg} {$dbArg}" .
            " > {$outArg}";

        $this->info("Creating backup: {$fullPath}");

        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0 || !File::exists($fullPath) || File::size($fullPath) === 0) {
            $this->error('Backup failed');
            $this->line("Command: {$cmd}");
            return self::FAILURE;
        }

        $this->info('Backup created successfully ✅');

        // Cleanup old backups
        $keep = max(1, (int) $this->option('keep'));
        $this->cleanupOldBackups($backupDir, $database, $keep);

        return self::SUCCESS;
    }

    private function cleanupOldBackups(string $dir, string $database, int $keep): void
    {
        $files = collect(File::files($dir))
            ->filter(fn ($f) =>
                str_starts_with($f->getFilename(), $database . '_') &&
                str_ends_with($f->getFilename(), '.sql')
            )
            ->sortByDesc(fn ($f) => $f->getMTime())
            ->values();

        $toDelete = $files->slice($keep);

        foreach ($toDelete as $file) {
            File::delete($file->getPathname());
        }

        if ($toDelete->count() > 0) {
            $this->info("Deleted {$toDelete->count()} old backups. Keeping last {$keep}.");
        }
    }
}
