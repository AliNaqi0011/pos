<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--tenant=}';
    protected $description = 'Create database backup';

    public function handle()
    {
        $tenantId = $this->option('tenant');
        $timestamp = now()->format('Y-m-d_H-i-s');
        
        $filename = $tenantId 
            ? "backup_tenant_{$tenantId}_{$timestamp}.sql"
            : "backup_full_{$timestamp}.sql";

        try {
            $this->info('Starting database backup...');
            
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $command = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                $host,
                $username,
                $password,
                $database,
                storage_path("app/backups/{$filename}")
            );

            if (!is_dir(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            exec($command, $output, $returnCode);

            if ($returnCode === 0) {
                $this->info("Backup created successfully: {$filename}");
                
                // Clean old backups (keep last 7 days)
                $this->cleanOldBackups();
            } else {
                $this->error('Backup failed');
            }

        } catch (\Exception $e) {
            $this->error('Backup error: ' . $e->getMessage());
        }
    }

    private function cleanOldBackups(): void
    {
        $backupPath = storage_path('app/backups');
        $files = glob($backupPath . '/backup_*.sql');
        
        foreach ($files as $file) {
            if (filemtime($file) < strtotime('-7 days')) {
                unlink($file);
            }
        }
    }
}