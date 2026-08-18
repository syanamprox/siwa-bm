<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class BackupController extends Controller
{
    use LogsActivity;

    private string $disk = 'local'; // storage/app/backups

    /**
     * GET /api/backup — list + status.
     */
    public function index(): JsonResponse
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $files = collect(glob($dir.'/*.zip') ?: [])
            ->map(fn ($path) => [
                'filename' => basename($path),
                'size' => filesize($path),
                'size_human' => $this->humanSize(filesize($path)),
                'created_at' => date('c', filemtime($path)),
            ])
            ->sortByDesc('created_at')
            ->values();

        return response()->json(['data' => [
            'backups' => $files,
            'status' => [
                'total_backups' => $files->count(),
                'total_size' => $files->sum('size'),
                'last_backup' => $files->first()['created_at'] ?? null,
            ],
        ]]);
    }

    /**
     * POST /api/backup — create (mysqldump + uploads → zip).
     */
    public function create(Request $request): JsonResponse
    {
        $filename = 'backup_'.now()->format('Y-m-d_His').'_'.Str::random(6).'.zip';
        $path = storage_path("app/backups/{$filename}");
        File::ensureDirectoryExists(dirname($path));

        $tempSql = storage_path("app/backups/{$filename}.temp.sql");

        try {
            // 1. Dump DB
            $c = config('database.connections.mysql');
            $cmd = sprintf(
                'mysqldump --single-transaction --routines --triggers --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
                escapeshellarg($c['username']),
                escapeshellarg($c['password']),
                escapeshellarg($c['host'] ?? '127.0.0.1'),
                escapeshellarg((string) ($c['port'] ?? 3306)),
                escapeshellarg($c['database']),
                escapeshellarg($tempSql)
            );
            exec($cmd, $out, $code);
            if ($code !== 0) {
                throw new \RuntimeException('mysqldump gagal: '.implode("\n", $out));
            }

            // 2. Zip: sql + uploads
            $zip = new ZipArchive();
            $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $zip->addFile($tempSql, 'database.sql');
            $uploadsDir = storage_path('app/public/documents');
            if (is_dir($uploadsDir)) {
                foreach (File::allFiles($uploadsDir) as $file) {
                    $zip->addFile($file->getPathname(), 'uploads/'.$file->getRelativePathname());
                }
            }
            $zip->close();
            File::delete($tempSql);

            $this->logActivity($request, 'backup', 'backup', "Buat backup {$filename}");

            return response()->json(['data' => ['filename' => $filename]], 201);
        } catch (\Throwable $e) {
            File::delete($tempSql);
            if (isset($path) && file_exists($path)) {
                File::delete($path);
            }

            return response()->json(['message' => 'Backup gagal: '.$e->getMessage()], 500);
        }
    }

    /**
     * GET /api/backup/{filename}/download
     */
    public function download(string $filename)
    {
        if (! preg_match('/^backup_[\w\-]+\.zip$/', $filename)) {
            return response()->json(['message' => 'Nama file tidak valid.'], 422);
        }
        $path = storage_path("app/backups/{$filename}");
        if (! file_exists($path)) {
            return response()->json(['message' => 'File backup tidak ditemukan.'], 404);
        }

        return response()->download($path);
    }

    /**
     * DELETE /api/backup/{filename}
     */
    public function destroy(Request $request, string $filename): JsonResponse
    {
        if (! preg_match('/^backup_[\w\-]+\.zip$/', $filename)) {
            return response()->json(['message' => 'Nama file tidak valid.'], 422);
        }
        $path = storage_path("app/backups/{$filename}");
        if (file_exists($path)) {
            File::delete($path);
            $this->logActivity($request, 'delete_backup', 'backup', "Hapus backup {$filename}");
        }

        return response()->json(['message' => 'Backup dihapus.']);
    }

    /**
     * POST /api/backup/restore — upload zip → restore DB + files.
     */
    public function restore(Request $request): JsonResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'mimes:zip', 'max:50000'],
        ]);

        $file = $request->file('backup_file');
        $tempDir = storage_path('app/restore_temp_'.Str::random(6));
        $zipPath = $file->storeAs('backups/restore', 'restore_'.now()->format('His').'.zip', 'local');

        try {
            $zip = new ZipArchive();
            if ($zip->open(storage_path("app/{$zipPath}")) !== true) {
                throw new \RuntimeException('File zip tidak valid.');
            }
            $zip->extractTo($tempDir);
            $zip->close();

            // Restore DB
            $sqlFile = $tempDir.'/database.sql';
            if (file_exists($sqlFile)) {
                $c = config('database.connections.mysql');
                $cmd = sprintf(
                    'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
                    escapeshellarg($c['username']),
                    escapeshellarg($c['password']),
                    escapeshellarg($c['host'] ?? '127.0.0.1'),
                    escapeshellarg((string) ($c['port'] ?? 3306)),
                    escapeshellarg($c['database']),
                    escapeshellarg($sqlFile)
                );
                exec($cmd, $out, $code);
                if ($code !== 0) {
                    throw new \RuntimeException('mysql restore gagal: '.implode("\n", $out));
                }
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
            }

            // Restore uploads
            $uploadsSrc = $tempDir.'/uploads';
            if (is_dir($uploadsSrc)) {
                File::copyDirectory($uploadsSrc, storage_path('app/public/documents'));
            }

            File::deleteDirectory($tempDir);
            Storage::disk('local')->delete($zipPath);

            $this->logActivity($request, 'restore', 'backup', 'Restore backup dari upload');

            return response()->json(['message' => 'Restore berhasil.']);
        } catch (\Throwable $e) {
            File::deleteDirectory($tempDir);
            Storage::disk('local')->delete($zipPath);

            return response()->json(['message' => 'Restore gagal: '.$e->getMessage()], 500);
        }
    }

    private function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }
}
