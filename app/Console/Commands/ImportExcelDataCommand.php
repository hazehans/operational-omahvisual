<?php

namespace App\Console\Commands;

use App\Models\Crew;
use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportExcelDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-excel-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import 370+ real customers and 110+ crew roster from Kasir LED 2026.xlsx';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai import data master Client & Crew dari Excel Kasir LED 2026...');

        $custFile = base_path('temp/imported_customers.json');
        $crewFile = base_path('temp/imported_crews.json');

        if (! file_exists($custFile) || ! file_exists($crewFile)) {
            $this->warn('File JSON belum ditemukan di temp/. Menjalankan ekstraktor python...');
            exec('python temp/export_excel_to_json.py');
        }

        if (! file_exists($custFile) || ! file_exists($crewFile)) {
            $this->error('Gagal menemukan data sumber ekspor Excel!');

            return self::FAILURE;
        }

        // 1. Import Customers
        $customersData = json_decode(file_get_contents($custFile), true);
        $this->info('Menemukan '.count($customersData).' data customer untuk diimpor...');

        $importedCust = 0;
        DB::transaction(function () use ($customersData, &$importedCust) {
            foreach ($customersData as $item) {
                Customer::updateOrCreate(
                    ['customer_code' => $item['customer_code']],
                    [
                        'name' => $item['name'],
                        'full_name' => $item['full_name'],
                        'organization' => $item['organization'],
                        'phone' => $item['phone'],
                        'address' => $item['address'],
                        'city' => $item['city'],
                        'notes' => $item['notes'],
                    ]
                );
                $importedCust++;
            }
        });

        $this->info("✓ Sukses mengimpor {$importedCust} data Customer!");

        // 2. Import Crews
        $crewsData = json_decode(file_get_contents($crewFile), true);
        $this->info('Menemukan '.count($crewsData).' data crew lapangan untuk diimpor...');

        $importedCrew = 0;
        DB::transaction(function () use ($crewsData, &$importedCrew) {
            foreach ($crewsData as $c) {
                Crew::updateOrCreate(
                    ['crew_code' => $c['crew_code']],
                    [
                        'nickname' => $c['nickname'],
                        'full_name' => $c['full_name'],
                        'phone' => $c['phone'],
                        'emergency_phone' => $c['emergency_phone'],
                        'position' => $c['position'],
                        'address' => $c['address'],
                        'city' => $c['city'],
                        'is_active' => $c['is_active'],
                    ]
                );
                $importedCrew++;
            }
        });

        $this->info("✓ Sukses mengimpor {$importedCrew} data Crew!");
        $this->newLine();
        $this->info('🎉 Seluruh data pelanggan riil dan roster kru Omah Visual LED telah siap digunakan di sistem!');

        return self::SUCCESS;
    }
}
