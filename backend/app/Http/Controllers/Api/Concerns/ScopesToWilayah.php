<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Scoping data per-role berdasarkan user_wilayahs.
 * admin/lurah: tanpa batas. rw: semua RT di bawah RW-nya. rt: RT miliknya.
 * Data domisili mengalir via keluargas.rt_id → wilayahs tree.
 */
trait ScopesToWilayah
{
    /**
     * RT wilayah IDs yang boleh diakses user (untuk role rw/rt).
     *
     * @return Collection<int>
     */
    private function rtIdsForUser($user): Collection
    {
        $wilayahIds = $user->userWilayah()->pluck('wilayah_id');

        if ($user->role === 'rw') {
            return Wilayah::whereIn('parent_id', $wilayahIds)->pluck('id');
        }

        return $wilayahIds; // role rt
    }

    private function isUnrestricted($user): bool
    {
        return in_array($user->role, ['admin', 'lurah'], true);
    }

    /**
     * Apply scope ke Keluarga query (whereIn rt_id).
     */
    private function scopeKeluarga(Builder $query): Builder
    {
        $user = request()->user();
        if ($this->isUnrestricted($user)) {
            return $query;
        }

        return $query->whereIn('rt_id', $this->rtIdsForUser($user));
    }

    /**
     * Apply scope ke Warga query (via relasi keluarga).
     * Catatan: warga tanpa KK tidak terlihat oleh rw/rt (konsisten dashboard lama).
     */
    private function scopeWarga(Builder $query): Builder
    {
        $user = request()->user();
        if ($this->isUnrestricted($user)) {
            return $query;
        }

        return $query->whereHas('keluarga', fn ($q) => $q->whereIn('rt_id', $this->rtIdsForUser($user)));
    }

    /**
     * Apply scope ke Iuran query (via relasi keluarga).
     */
    private function scopeIuran(Builder $query): Builder
    {
        return $this->scopeViaKeluarga($query);
    }

    /**
     * Generic scope untuk model yang punya relasi keluarga (Iuran).
     */
    private function scopeViaKeluarga(Builder $query): Builder
    {
        $user = request()->user();
        if ($this->isUnrestricted($user)) {
            return $query;
        }

        return $query->whereHas('keluarga', fn ($q) => $q->whereIn('rt_id', $this->rtIdsForUser($user)));
    }
}
