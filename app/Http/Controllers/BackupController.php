<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Display backup management page
     */
    public function index()
    {
        $backups = $this->getBackupList();

        return view('admin.backup.index', compact('backups'));
    }

    /**
     * Get list of available backups
     */
    private function getBackupList()
    {
        $backupPath = storage_path('app/backups');
        $backups = [];

        if (File::exists($backupPath)) {
            $files = File::allFiles($backupPath);

            foreach ($files as $file) {
                if ($file->getExtension() === 'zip') {
                    $backups[] = [
                        'filename' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'size' => $this->formatBytes($file->getSize()),
                        'created_at' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                        'download_url' => route('backup.download', $file->getFilename())
                    ];
                }
            }

            // Sort by created_at descending
            usort($backups, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        return $backups;
    }

    /**
     * Create backup
     */
    public function create(Request $request)
    {
        try {
            // Generate backup filename
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '_' . str_random(8) . '.zip';
            $backupPath = storage_path('app/backups/' . $filename);

            // Create backups directory if not exists
            $this->ensureBackupDirectory();

            // Generate backup
            $this->generateBackup($backupPath);

            // Get backup info
            $backupInfo = [
                'filename' => $filename,
                'size' => $this->formatBytes(filesize($backupPath)),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];

            // Log activity
            if (auth()->check()) {
                \App\Models\AktivitasLog::create([
                    'user_id' => auth()->id(),
                    'tabel_referensi' => 'backups',
                    'id_referensi' => null,
                    'jenis_aktivitas' => 'create',
                    'deskripsi' => "Membuat backup: {$filename}",
                    'data_baru' => json_encode($backupInfo)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil dibuat',
                'data' => $backupInfo
            ]);

        } catch (\Exception $e) {
            Log::error('Backup creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download backup
     */
    public function download($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);

        if (!File::exists($backupPath)) {
            abort(404, 'File backup tidak ditemukan');
        }

        return response()->download($backupPath, $filename, [
            'Content-Type' => 'application/zip',
            'Content-Length' => filesize($backupPath),
        ]);
    }

    /**
     * Delete backup
     */
    public function delete($filename)
    {
        try {
            $backupPath = storage_path('app/backups/' . $filename);

            if (!File::exists($backupPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan'
                ], 404);
            }

            // Get backup info before deletion
            $backupInfo = [
                'filename' => $filename,
                'size' => $this->formatBytes(filesize($backupPath))
            ];

            // Delete file
            File::delete($backupPath);

            // Log activity
            if (auth()->check()) {
                \App\Models\AktivitasLog::create([
                    'user_id' => auth()->id(),
                    'tabel_referensi' => 'backups',
                    'id_referensi' => null,
                    'jenis_aktivitas' => 'delete',
                    'deskripsi' => "Menghapus backup: {$filename}",
                    'data_lama' => json_encode($backupInfo)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Backup deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore from backup
     */
    public function restore(Request $request)
    {
        try {
            $request->validate([
                'backup_file' => 'required|file|mimes:zip|max:50000'
            ]);

            $backupFile = $request->file('backup_file');
            $filename = 'restore_' . date('Y-m-d_H-i-s') . '_' . $backupFile->getClientOriginalName();
            $backupPath = storage_path('app/backups/restore/' . $filename);

            // Create restore directory
            $this->ensureRestoreDirectory();

            // Upload backup file
            $backupFile->storeAs('backups/restore', $filename);

            // Extract and restore
            $this->extractAndRestore($backupPath);

            // Log activity
            if (auth()->check()) {
                \App\Models\AktivitasLog::create([
                    'user_id' => auth()->id(),
                    'tabel_referensi' => 'restores',
                    'id_referensi' => null,
                    'jenis_aktivitas' => 'restore',
                    'deskripsi' => "Restore dari backup: {$filename}",
                    'data_baru' => json_encode(['filename' => $filename])
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Restore berhasil dilakukan. Database telah dikembalikan ke kondisi backup.'
            ]);

        } catch (\Exception $e) {
            Log::error('Restore failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal restore: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get backup status and info
     */
    public function status()
    {
        try {
            $backupPath = storage_path('app/backups');
            $backups = $this->getBackupList();

            $lastBackup = !empty($backups) ? $backups[0]['created_at'] : null;
            $totalBackups = count($backups);
            $totalSize = 0;

            foreach ($backups as $backup) {
                $totalSize += $backup['size_bytes'] ?? 0;
            }

            // Check database size
            $databaseSize = $this->getDatabaseSize();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_backups' => $totalBackups,
                    'last_backup' => $lastBackup,
                    'total_size' => $this->formatBytes($totalSize),
                    'database_size' => $databaseSize,
                    'backup_directory' => $backupPath,
                    'backup_directory_exists' => File::exists($backupPath)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate backup file
     */
    private function generateBackup($backupPath)
    {
        // Include database dumps and storage
        $this->dumpDatabase($backupPath);
        $this->includeStorage($backupPath);
    }

    /**
     * Dump database to backup
     */
    private function dumpDatabase($backupPath)
    {
        $command = sprintf(
            'mysqldump --single-transaction --routines --triggers --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.port'),
            config('database.connections.mysql.database'),
            $backupPath . '_temp.sql'
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Database dump failed: ' . implode("\n", $output));
        }
    }

    /**
     * Include storage files in backup
     */
    private function includeStorage($backupPath)
    {
        $zip = new \ZipArchive();
        $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Add database dump
        $zip->addFile($backupPath . '_temp.sql', 'database.sql');

        // Add important directories
        $directories = [
            storage_path('app/public/uploads'),
            storage_path('app/public/storage')
        ];

        foreach ($directories as $dir) {
            if (File::exists($dir)) {
                $this->addDirectoryToZip($zip, $dir, basename($dir));
            }
        }

        $zip->close();

        // Remove temporary file
        File::delete($backupPath . '_temp.sql');
    }

    /**
     * Add directory and its contents to zip
     */
    private function addDirectoryToZip($zip, $dir, $zipPath)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $zipPath . '/' . $relativePath);
            }
        }
    }

    /**
     * Extract and restore from backup
     */
    private function extractAndRestore($backupPath)
    {
        $extractPath = storage_path('app/restore_temp');

        // Clean previous extract
        if (File::exists($extractPath)) {
            File::deleteDirectory($extractPath);
        }

        // Extract backup
        $zip = new \ZipArchive();
        if ($zip->open($backupPath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        }

        // Restore database
        $sqlFile = $extractPath . '/database.sql';
        if (File::exists($sqlFile)) {
            $this->restoreDatabase($sqlFile);
        }

        // Restore files if exists
        $this->restoreFiles($extractPath);

        // Clean up
        File::deleteDirectory($extractPath);
    }

    /**
     * Restore database from SQL file
     */
    private function restoreDatabase($sqlFile)
    {
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.port'),
            config('database.connections.mysql.database'),
            $sqlFile
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Database restore failed: ' . implode("\n", $output));
        }

        // Clear cache
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
    }

    /**
     * Restore files from backup
     */
    private function restoreFiles($extractPath)
    {
        $targetDirs = [
            $extractPath . '/storage/app/public/uploads' => storage_path('app/public/uploads'),
            $extractPath . '/storage/app/public/storage' => storage_path('app/public/storage'),
        ];

        foreach ($targetDirs as $source => $target) {
            if (File::exists($source)) {
                if (!File::exists($target)) {
                    File::makeDirectory($target, 0755, true);
                }
                $this->copyDirectory($source, $target);
            }
        }
    }

    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $destination)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                if (!File::exists($target)) {
                    File::makeDirectory($target, 0755, true);
                }
            } else {
                copy($item, $target);
            }
        }
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        $database = config('database.connections.mysql.database');
        $query = "SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size' FROM information_schema.tables WHERE table_schema = '{$database}'";

        $result = DB::select($query);
        $totalSize = array_sum(array_column($result, 'size'));

        return $this->formatBytes($totalSize * 1024 * 1024);
    }

    /**
     * Ensure backup directory exists
     */
    private function ensureBackupDirectory()
    {
        $backupPath = storage_path('app/backups');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }
    }

    /**
     * Ensure restore directory exists
     */
    private function ensureRestoreDirectory()
    {
        $restorePath = storage_path('app/backups/restore');
        if (!File::exists($restorePath)) {
            File::makeDirectory($restorePath, 0755, true);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}