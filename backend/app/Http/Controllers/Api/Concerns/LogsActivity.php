<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\AktivitasLog;
use Illuminate\Http\Request;

/**
 * Audit trail — kolom SESUAI migration aktivitas_logs:
 * user_id, action, module, description, old_data, new_data, ip_address, user_agent.
 * (Jangan pakai helper statis lama — kolomnya phantom.)
 */
trait LogsActivity
{
    private function logActivity(Request $request, string $action, string $module, string $description, ?array $old = null, ?array $new = null): void
    {
        try {
            AktivitasLog::create([
                'user_id' => $request->user()?->id,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'old_data' => $old ? json_encode($old) : null,
                'new_data' => $new ? json_encode($new) : null,
                'ip_address' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? 'Unknown', 0, 255),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal menulis activity log: ' . $e->getMessage());
        }
    }
}
