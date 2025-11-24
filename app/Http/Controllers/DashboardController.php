<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Warga;
use App\Models\Keluarga;
use App\Models\Iuran;
use App\Models\PembayaranIuran;
use App\Models\Wilayah;

class DashboardController extends Controller
{
    /**
     * Display the dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = [];

        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard($data);
            case 'lurah':
                return $this->lurahDashboard($data);
            case 'rw':
                return $this->rwDashboard($data);
            case 'rt':
                return $this->rtDashboard($data);
            default:
                return redirect('/logout');
        }
    }

    /**
     * Admin Dashboard - Full overview
     */
    private function adminDashboard($data)
    {
        $data['total_warga'] = Warga::count();
        $data['total_keluarga'] = Keluarga::count();
        $data['total_rt'] = Wilayah::where('tingkat', 'RT')->count();
        $data['total_rw'] = Wilayah::where('tingkat', 'RW')->count();
        $data['total_iuran_bulanan'] = Iuran::where('status', 'belum_bayar')->sum('nominal');
        $data['pemasukan_bulan_ini'] = PembayaranIuran::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');

        // Get demographics data for charts
        $data['demografi_jenis_kelamin'] = Warga::selectRaw('jenis_kelamin, COUNT(*) as total')
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin');

        $data['demografi_pendidikan'] = Warga::selectRaw('pendidikan_terakhir, COUNT(*) as total')
            ->groupBy('pendidikan_terakhir')
            ->pluck('total', 'pendidikan_terakhir');

        // Recent activities
        $data['recent_activities'] = \App\Models\AktivitasLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.admin', compact('data'));
    }

    /**
     * Lurah Dashboard - Kelurahan level overview
     */
    private function lurahDashboard($data)
    {
        $data['total_warga'] = Warga::count();
        $data['total_keluarga'] = Keluarga::count();
        $data['total_rw'] = Wilayah::where('tingkat', 'RW')->count();
        $data['total_rt'] = Wilayah::where('tingkat', 'RT')->count();

        // Iuran statistics for kelurahan
        $data['total_tagihan_iuran'] = Iuran::where('status', 'belum_bayar')->sum('nominal');
        $data['pemasukan_bulan_ini'] = PembayaranIuran::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');

        // Warga per RW (corrected query using proper relationships)
        $data['warga_per_rw'] = Warga::selectRaw('COUNT(*) as total, rw.nama as rw_nama')
            ->join('keluargas', 'warga.kk_id', '=', 'keluargas.id')
            ->join('wilayah as rt', 'keluargas.rt_id', '=', 'rt.id')
            ->join('wilayah as rw', 'rt.parent_id', '=', 'rw.id')
            ->groupBy('rw.id', 'rw.nama')
            ->orderBy('rw.nama')
            ->get();

        return view('dashboard.lurah', compact('data'));
    }

    /**
     * RW Dashboard - RW level overview
     */
    private function rwDashboard($data)
    {
        // Get RW areas this user has access to
        $rw_areas = $this->getUserWilayah(Auth::user(), 'RW');

        if ($rw_areas->isEmpty()) {
            return view('dashboard.no-access', compact('data'));
        }

        $rw_ids = $rw_areas->pluck('id');
        $rt_ids = Wilayah::whereIn('parent_id', $rw_ids)->pluck('id');

        $data['total_warga'] = Warga::join('keluargas', 'wargas.kk_id', '=', 'keluargas.id')
            ->whereIn('keluargas.rt_id', $rt_ids)
            ->count();
        $data['total_keluarga'] = Keluarga::whereIn('rt_id', $rt_ids)->count();
        $data['total_rt'] = Wilayah::whereIn('parent_id', $rw_ids)->count();

        // Iuran statistics for RW (using keluarga relationships)
        $keluarga_ids = Keluarga::whereIn('rt_id', $rt_ids)->pluck('id');
        $data['total_tagihan_iuran'] = Iuran::whereIn('kk_id', $keluarga_ids)
            ->where('status', 'belum_bayar')
            ->sum('nominal');

        $iuran_ids = Iuran::whereIn('kk_id', $keluarga_ids)->pluck('id');
        $data['pemasukan_bulan_ini'] = PembayaranIuran::whereIn('iuran_id', $iuran_ids)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');

        // Warga per RT in this RW
        $data['warga_per_rt'] = Warga::selectRaw('rt.nama as rt_nama, COUNT(*) as total')
            ->join('keluargas', 'wargas.kk_id', '=', 'keluargas.id')
            ->join('wilayah as rt', 'keluargas.rt_id', '=', 'rt.id')
            ->whereIn('keluargas.rt_id', $rt_ids)
            ->groupBy('rt.id', 'rt.nama')
            ->orderBy('rt.nama')
            ->get();

        return view('dashboard.rw', compact('data'));
    }

    /**
     * RT Dashboard - RT level overview
     */
    private function rtDashboard($data)
    {
        // Get RT areas this user has access to
        $rt_areas = $this->getUserWilayah(Auth::user(), 'RT');

        if ($rt_areas->isEmpty()) {
            return view('dashboard.no-access', compact('data'));
        }

        $rt_ids = $rt_areas->pluck('id');

        $data['total_warga'] = Warga::join('keluargas', 'wargas.kk_id', '=', 'keluargas.id')
            ->whereIn('keluargas.rt_id', $rt_ids)
            ->count();
        $data['total_keluarga'] = Keluarga::whereIn('rt_id', $rt_ids)->count();

        // Iuran statistics for RT
        $keluarga_ids = Keluarga::whereIn('rt_id', $rt_ids)->pluck('id');
        $data['total_tagihan_iuran'] = Iuran::whereIn('kk_id', $keluarga_ids)
            ->where('status', 'belum_bayar')
            ->sum('nominal');

        $iuran_ids = Iuran::whereIn('kk_id', $keluarga_ids)->pluck('id');
        $data['pemasukan_bulan_ini'] = PembayaranIuran::whereIn('iuran_id', $iuran_ids)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');

        // Recent payments in this RT
        $data['recent_payments'] = PembayaranIuran::whereIn('iuran_id', $iuran_ids)
            ->with(['iuran.keluarga', 'petugas'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Pending iuran list
        $data['pending_iuran'] = Iuran::whereIn('kk_id', $keluarga_ids)
            ->where('status', 'belum_bayar')
            ->with(['keluarga', 'jenisIuran'])
            ->orderBy('jatuh_tempo', 'asc')
            ->take(10)
            ->get();

        return view('dashboard.rt', compact('data'));
    }

    /**
     * Get wilayah access for user based on role
     */
    private function getUserWilayah($user, $tingkat)
    {
        if ($user->role === 'admin' || $user->role === 'lurah') {
            return Wilayah::where('tingkat', $tingkat)->get();
        }

        return Wilayah::where('tingkat', $tingkat)
            ->whereHas('userWilayah', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();
    }

    /**
     * API endpoint for dashboard statistics (AJAX calls)
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'monthly'); // monthly, yearly, etc.

        switch ($user->role) {
            case 'admin':
                return $this->getAdminStats($period);
            case 'lurah':
                return $this->getLurahStats($period);
            case 'rw':
                return $this->getRwStats($period);
            case 'rt':
                return $this->getRtStats($period);
            default:
                return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    private function getAdminStats($period)
    {
        // Implementation for admin statistics
        return response()->json([
            'warga_growth' => $this->getWargaGrowth($period),
            'iuran_revenue' => $this->getIuranRevenue($period),
            'demographic_changes' => $this->getDemographicChanges($period)
        ]);
    }

    // Additional helper methods for statistics...
    private function getWargaGrowth($period)
    {
        // Implementation for warga growth statistics
        return [];
    }

    private function getIuranRevenue($period)
    {
        // Implementation for iuran revenue statistics
        return [];
    }

    private function getDemographicChanges($period)
    {
        // Implementation for demographic changes
        return [];
    }
}
