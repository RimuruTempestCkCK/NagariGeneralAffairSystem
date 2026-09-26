<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Kendaraan;
use App\Models\Atk;
use App\Models\PermintaanAtk;
use App\Models\Keamanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }
        return $this->staffDashboard($user);
    }

    private function adminDashboard()
    {
        $totalAtk = Atk::count();
        $totalStok = Atk::sum('jumlah');
        $pendingPermintaan = PermintaanAtk::where('status', 'Pending')->count();
        
        $totalKendaraan = Kendaraan::count();
        // Check vehicles where stnk is expiring in <= 30 days
        $expiringStnk = Kendaraan::whereNotNull('jatuh_tempo_stnk')
            ->whereDate('jatuh_tempo_stnk', '<=', Carbon::now()->addDays(30))
            ->count();
            
        $totalAset = Aset::count();
        $expiringAset = Aset::whereNotNull('jatuh_tempo_sertifikat')
            ->whereDate('jatuh_tempo_sertifikat', '<=', Carbon::now()->addDays(90))
            ->count();
            
        $totalKeamanan = Keamanan::count();
        
        $recentTrans = PermintaanAtk::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard.index', compact(
            'totalAtk', 'totalStok', 'pendingPermintaan',
            'totalKendaraan', 'expiringStnk',
            'totalAset', 'expiringAset',
            'totalKeamanan', 'recentTrans'
        ));
    }

    private function staffDashboard($user)
    {
        $myPermintaan = PermintaanAtk::where('user_id', $user->id)->count();
        $pendingPermintaan = PermintaanAtk::where('user_id', $user->id)->where('status', 'Pending')->count();
        
        $myKeamanan = Keamanan::where('user_id', $user->id)->count();
        
        $recentTrans = PermintaanAtk::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();

        return view('staff.dashboard.index', compact(
            'myPermintaan', 'pendingPermintaan', 'myKeamanan', 'recentTrans'
        ));
    }
}

