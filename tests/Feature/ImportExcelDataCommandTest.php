<?php

namespace Tests\Feature;

use App\Models\Crew;
use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ImportExcelDataCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_import_excel_data_command_runs_successfully(): void
    {
        $this->artisan('app:import-excel-data')
            ->expectsOutputToContain('Memulai import data master Client & Crew')
            ->expectsOutputToContain('Sukses mengimpor')
            ->assertSuccessful();

        // Verify customers imported
        $this->assertGreaterThan(300, Customer::count());
        $kholiq = Customer::where('name', 'Mas Kholiq')->first();
        $this->assertNotNull($kholiq);
        $this->assertEquals('Singham Multimedia', $kholiq->organization);
        $this->assertEquals('08113035202', $kholiq->phone);

        // Verify crew imported
        $this->assertGreaterThan(100, Crew::count());
        $boby = Crew::where('nickname', 'Boby')->first();
        $this->assertNotNull($boby);
        $this->assertEquals('Bobyvio Mirza Lavfidi', $boby->full_name);
        $this->assertEquals('085954314751', $boby->phone);
    }
}
