<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WipeSimulationDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wipe-simulation-data {--force : Jalankan tanpa konfirmasi prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan seluruh data master barang, aset, customer, pesanan, invoice, servis, dan stok untuk memulai simulasi sistem dari awal';

    /**
     * Daftar tabel operasional dan master data yang dibersihkan.
     *
     * @var array<string>
     */
    protected array $targetTables = [
        'service_spareparts',
        'service_tickets',
        'invoice_payments',
        'invoice_items',
        'invoices',
        'spk_checklist_items',
        'spk_assignments',
        'event_vehicles',
        'event_crew_assignments',
        'order_led_screens',
        'event_orders',
        'customers',
        'crews',
        'vehicles',
        'stock_movement_items',
        'stock_movements',
        'stock_adjustment_items',
        'stock_adjustments',
        'inventory_count_items',
        'inventory_counts',
        'stock_balances',
        'assets',
        'products',
        'audit_logs',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('================================================================');
        $this->warn(' PERINGATAN: PEMBERSIHAN DATA OPERASIONAL & SIMULASI');
        $this->warn('================================================================');
        $this->line('Tabel yang akan dikosongkan (0 data):');
        foreach ($this->targetTables as $table) {
            $this->line(" - {$table}");
        }
        $this->newLine();
        $this->info('Tabel yang DIPERTAHANKAN (Aman):');
        $this->line(' - users (Akun login Owner, Warehouse, Ops, Finance, Teknisi, Superuser)');
        $this->line(' - roles & permissions (Hak akses Spatie)');
        $this->line(' - units (Satuan ukuran: Pcs, Unit, Meter, Roll, Set, Box)');
        $this->line(' - categories (Kategori barang)');
        $this->line(' - locations (Titik lokasi gudang, lemari, staging, workshop)');
        $this->line(' - migrations');
        $this->newLine();

        if (! $this->option('force') && ! $this->confirm('Apakah Anda yakin ingin mengosongkan seluruh data di atas untuk memulai simulasi dari awal?', false)) {
            $this->info('Aksi pembersihan dibatalkan.');

            return self::SUCCESS;
        }

        $this->info('Memulai pengosongan data via TRUNCATE CASCADE...');

        try {
            $tableList = implode(', ', array_map(fn ($t) => '"'.$t.'"', $this->targetTables));
            DB::statement("TRUNCATE TABLE {$tableList} RESTART IDENTITY CASCADE;");
            $this->info('Berhasil mengosongkan seluruh tabel operasional dan mereset ID sequence.');

            // Bersihkan file upload fisik di storage jika ada
            $uploadDirs = [
                storage_path('app/public/adjustments'),
                storage_path('app/public/service_tickets'),
                storage_path('app/public/products'),
                storage_path('app/public/customers'),
                storage_path('app/public/spk_qc'),
                storage_path('app/public/invoices'),
            ];

            foreach ($uploadDirs as $dir) {
                if (File::isDirectory($dir)) {
                    File::cleanDirectory($dir);
                    $this->line("Dibersihkan direktori: {$dir}");
                }
            }

            $this->newLine();
            $this->info('================================================================');
            $this->info(' STATUS DATABASE BERHASIL DIRESET KE KONDISI SIMULASI AWAL');
            $this->info('================================================================');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal membersihkan data: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
