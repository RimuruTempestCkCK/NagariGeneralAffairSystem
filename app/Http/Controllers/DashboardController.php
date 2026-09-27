<?php

namespace App\Http\Controllers;

use App\Models\Aset;
use App\Models\Kendaraan;
use App\Models\Atk;
use App\Models\PermintaanAtk;
use App\Models\Keamanan;
use App\Models\PerjalananKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === "admin") {
            return $this->adminDashboard();
        }
        return $this->staffDashboard($user);
    }

    private function getLast6MonthsLabels()
    {
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subMonths($i)->format("M Y");
        }
        return $labels;
    }

    private function fillMonthlyData($queryResult)
    {
        $data = [];
        $resultMap = $queryResult->keyBy("month_year")->toArray();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format("Y-m");
            $data[] = isset($resultMap[$key]) ? (int) $resultMap[$key]["total"] : 0;
        }

        return $data;
    }

    private function adminDashboard()
    {
        $totalAtk = Atk::count();
        $totalStok = Atk::sum("jumlah");
        $pendingPermintaan = PermintaanAtk::where("status", "PENDING")->count();
        
        $totalKendaraan = Kendaraan::count();
        $expiringStnk = Kendaraan::whereNotNull("jatuh_tempo_stnk")
            ->whereDate("jatuh_tempo_stnk", "<=", Carbon::now()->addDays(30))
            ->count();
            
        $totalAset = Aset::count();
        $expiringAset = Aset::whereNotNull("jatuh_tempo_sertifikat")
            ->whereDate("jatuh_tempo_sertifikat", "<=", Carbon::now()->addDays(90))
            ->count();
            
        $totalKeamanan = Keamanan::count();
        $recentTrans = PermintaanAtk::with("user")->orderBy("created_at", "desc")->take(5)->get();

        // CHART A: Permintaan ATK berdasarkan status
        $permintaanByStatusRaw = PermintaanAtk::select("status", DB::raw("COUNT(*) as total"))
            ->groupBy("status")
            ->pluck("total", "status")
            ->toArray();
            
        $permintaanByStatus = [
            "DRAFT" => $permintaanByStatusRaw["DRAFT"] ?? 0,
            "PENDING" => $permintaanByStatusRaw["PENDING"] ?? 0,
            "APPROVED" => $permintaanByStatusRaw["APPROVED"] ?? 0,
            "REJECTED" => $permintaanByStatusRaw["REJECTED"] ?? 0,
            "COMPLETED" => $permintaanByStatusRaw["COMPLETED"] ?? 0,
        ];

        // CHART B: Tren Permintaan ATK (6 Bulan)
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $isSqlite = DB::getDriverName() === 'sqlite';
        $dateFormatSql = $isSqlite ? "strftime('%Y-%m', tanggal_permintaan)" : "DATE_FORMAT(tanggal_permintaan, '%Y-%m')";

        $trenPermintaanRaw = PermintaanAtk::select(DB::raw("{$dateFormatSql} as month_year"), DB::raw("COUNT(*) as total"))
            ->where("tanggal_permintaan", ">=", $sixMonthsAgo)
            ->groupBy("month_year")
            ->get();
            
        $trenPermintaan = [
            "labels" => $this->getLast6MonthsLabels(),
            "data" => $this->fillMonthlyData($trenPermintaanRaw)
        ];

        // CHART C: Aktivitas Perjalanan Kendaraan (6 Bulan)
        $dateFormatPerjalananSql = $isSqlite ? "strftime('%Y-%m', tanggal)" : "DATE_FORMAT(tanggal, '%Y-%m')";
        $trenPerjalananRaw = PerjalananKendaraan::select(DB::raw("{$dateFormatPerjalananSql} as month_year"), DB::raw("COUNT(*) as total"))
            ->where("tanggal", ">=", $sixMonthsAgo)
            ->groupBy("month_year")
            ->get();
            
        $trenPerjalanan = [
            "labels" => $this->getLast6MonthsLabels(),
            "data" => $this->fillMonthlyData($trenPerjalananRaw)
        ];

        return view("admin.dashboard.index", compact(
            "totalAtk", "totalStok", "pendingPermintaan",
            "totalKendaraan", "expiringStnk",
            "totalAset", "expiringAset",
            "totalKeamanan", "recentTrans",
            "permintaanByStatus", "trenPermintaan", "trenPerjalanan"
        ));
    }

    private function staffDashboard($user)
    {
        $myPermintaan = PermintaanAtk::where("user_id", $user->id)->count();
        $pendingPermintaan = PermintaanAtk::where("user_id", $user->id)->where("status", "PENDING")->count();
        $myKeamanan = Keamanan::where("user_id", $user->id)->count();
        $recentTrans = PermintaanAtk::where("user_id", $user->id)->orderBy("created_at", "desc")->take(5)->get();

        // CHART A: Status Permintaan Pribadi
        $permintaanByStatusRaw = PermintaanAtk::where("user_id", $user->id)
            ->select("status", DB::raw("COUNT(*) as total"))
            ->groupBy("status")
            ->pluck("total", "status")
            ->toArray();
            
        $permintaanByStatus = [
            "DRAFT" => $permintaanByStatusRaw["DRAFT"] ?? 0,
            "PENDING" => $permintaanByStatusRaw["PENDING"] ?? 0,
            "APPROVED" => $permintaanByStatusRaw["APPROVED"] ?? 0,
            "REJECTED" => $permintaanByStatusRaw["REJECTED"] ?? 0,
            "COMPLETED" => $permintaanByStatusRaw["COMPLETED"] ?? 0,
        ];

        // CHART B: Tren Permintaan ATK Pribadi (6 Bulan)
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $isSqlite = DB::getDriverName() === 'sqlite';
        $dateFormatSql = $isSqlite ? "strftime('%Y-%m', tanggal_permintaan)" : "DATE_FORMAT(tanggal_permintaan, '%Y-%m')";

        $trenPermintaanRaw = PermintaanAtk::where("user_id", $user->id)
            ->select(DB::raw("{$dateFormatSql} as month_year"), DB::raw("COUNT(*) as total"))
            ->where("tanggal_permintaan", ">=", $sixMonthsAgo)
            ->groupBy("month_year")
            ->get();
            
        $trenPermintaan = [
            "labels" => $this->getLast6MonthsLabels(),
            "data" => $this->fillMonthlyData($trenPermintaanRaw)
        ];

        return view("staff.dashboard.index", compact(
            "myPermintaan", "pendingPermintaan", "myKeamanan", "recentTrans",
            "permintaanByStatus", "trenPermintaan"
        ));
    }
}

