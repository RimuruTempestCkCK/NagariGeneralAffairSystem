<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kendaraan;
use App\Models\Aset;
use App\Services\NotificationService;
use Carbon\Carbon;

class CheckDocumentExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gas:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check STNK and Asset Certificate expiry and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting document expiry check...');
        $today = Carbon::today();
        
        // Configurable thresholds, defaulting to reasonable business logic
        $stnkWarningDays = config('gas.stnk_warning_days', 30);
        $assetWarningDays = config('gas.asset_warning_days', 60);

        // 1. Check STNK Kendaraan
        $kendaraans = Kendaraan::whereNotNull('jatuh_tempo_stnk')->get();
        foreach ($kendaraans as $k) {
            $expiryDate = Carbon::parse($k->jatuh_tempo_stnk)->startOfDay();
            $diffDays = $today->diffInDays($expiryDate, false); // false = return negative if past

            if ($diffDays < 0) {
                // Expired
                $uniqueKey = "STNK_EXPIRED:{$k->id}:{$k->jatuh_tempo_stnk}";
                NotificationService::notifyAdminDocumentExpiry(
                    'STNK_EXPIRED',
                    'STNK Kendaraan Kadaluarsa',
                    "STNK kendaraan {$k->plat_nomor} telah kadaluarsa sejak " . abs($diffDays) . " hari lalu.",
                    route('kendaraan.index'),
                    $k->id,
                    $uniqueKey
                );
            } elseif ($diffDays <= $stnkWarningDays) {
                // Expiring
                $uniqueKey = "STNK_EXPIRING:{$k->id}:{$k->jatuh_tempo_stnk}";
                NotificationService::notifyAdminDocumentExpiry(
                    'STNK_EXPIRING',
                    'STNK Kendaraan Akan Jatuh Tempo',
                    "STNK kendaraan {$k->plat_nomor} akan jatuh tempo dalam {$diffDays} hari.",
                    route('kendaraan.index'),
                    $k->id,
                    $uniqueKey
                );
            }
        }
        $this->info('STNK check completed.');

        // 2. Check Sertifikat Aset
        $asets = Aset::whereNotNull('jatuh_tempo_sertifikat')->get();
        foreach ($asets as $aset) {
            $expiryDate = Carbon::parse($aset->jatuh_tempo_sertifikat)->startOfDay();
            $diffDays = $today->diffInDays($expiryDate, false);

            if ($diffDays < 0) {
                // Expired
                $uniqueKey = "ASSET_CERT_EXPIRED:{$aset->id}:{$aset->jatuh_tempo_sertifikat}";
                NotificationService::notifyAdminDocumentExpiry(
                    'ASSET_CERT_EXPIRED',
                    'Sertifikat Aset Kadaluarsa',
                    "Sertifikat {$aset->nomor_sertifikat} pada aset {$aset->kode_cabang} telah kadaluarsa.",
                    route('aset.index'),
                    $aset->id,
                    $uniqueKey
                );
            } elseif ($diffDays <= $assetWarningDays) {
                // Expiring
                $uniqueKey = "ASSET_CERT_EXPIRING:{$aset->id}:{$aset->jatuh_tempo_sertifikat}";
                NotificationService::notifyAdminDocumentExpiry(
                    'ASSET_CERT_EXPIRING',
                    'Sertifikat Aset Akan Jatuh Tempo',
                    "Sertifikat {$aset->nomor_sertifikat} pada aset {$aset->kode_cabang} akan jatuh tempo dalam {$diffDays} hari.",
                    route('aset.index'),
                    $aset->id,
                    $uniqueKey
                );
            }
        }
        $this->info('Asset Certificate check completed.');
        $this->info('Done.');
    }
}
