<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SuperuserDatabaseController extends Controller
{
    /**
     * Daftar tabel yang diizinkan untuk diakses & dimanipulasi raw.
     *
     * @var array<string, array{name: string, category: string, description: string}>
     */
    protected array $allowedTables = [
        'products' => [
            'name' => 'Master Produk',
            'category' => 'Master Data',
            'description' => 'Katalog SKU modul LED, kabinet, processor, kabel, dan sparepart',
        ],
        'assets' => [
            'name' => 'Unit Serial Aset',
            'category' => 'Inventaris',
            'description' => 'Aset individual bernomor seri pabrik (25 Processor, TV, Toolbox)',
        ],
        'categories' => [
            'name' => 'Kategori Barang',
            'category' => 'Master Data',
            'description' => 'Hierarki pengelompokan produk dan perangkat LED',
        ],
        'units' => [
            'name' => 'Satuan Ukur',
            'category' => 'Master Data',
            'description' => 'Daftar unit pengukuran inventori (Unit, Pcs, Roll, Meter)',
        ],
        'locations' => [
            'name' => 'Titik Gudang & Lemari',
            'category' => 'Master Data',
            'description' => 'Zona penyimpanan fisik (Lemari 1-3, Staging LED, Workshop)',
        ],
        'stock_balances' => [
            'name' => 'Saldo Fisik Kuantitas',
            'category' => 'Inventaris',
            'description' => 'Rekap kuantitas stok per lokasi, kepemilikan, dan kondisi',
        ],
        'stock_movements' => [
            'name' => 'Transaksi Mutasi Stok',
            'category' => 'Transaksi',
            'description' => 'Header catatan pergerakan masuk (Inbound), keluar (Outbound), transfer',
        ],
        'stock_movement_items' => [
            'name' => 'Item Rincian Mutasi',
            'category' => 'Transaksi',
            'description' => 'Baris rincian item barang pada setiap nomor bukti mutasi',
        ],
        'stock_adjustments' => [
            'name' => 'Pengajuan Koreksi Stok',
            'category' => 'Transaksi',
            'description' => 'Pengajuan penyesuaian selisih fisik dengan approval Owner',
        ],
        'stock_adjustment_items' => [
            'name' => 'Item Rincian Koreksi',
            'category' => 'Transaksi',
            'description' => 'Daftar item produk yang mengalami penyesuaian kuantitas',
        ],
        'inventory_counts' => [
            'name' => 'Sesi Stock Opname',
            'category' => 'Transaksi',
            'description' => 'Sesi audit fisik dan berita acara pengesahan stok awal',
        ],
        'inventory_count_items' => [
            'name' => 'Item Rincian Opname',
            'category' => 'Transaksi',
            'description' => 'Data komparasi stok sistem vs hitungan fisik lapangan',
        ],
        'audit_logs' => [
            'name' => 'Audit Trail Sistem',
            'category' => 'Sistem & Keamanan',
            'description' => 'Log riwayat seluruh aktivitas dan perubahan data pengguna',
        ],
        'users' => [
            'name' => 'Akun Pengguna',
            'category' => 'Sistem & Keamanan',
            'description' => 'Master kredensial pengguna aplikasi Omah Visual',
        ],
        'roles' => [
            'name' => 'Peran (Roles)',
            'category' => 'Sistem & Keamanan',
            'description' => 'Daftar peran hak akses (Owner, Warehouse, Admin LED, Superuser)',
        ],
        'permissions' => [
            'name' => 'Hak Akses (Permissions)',
            'category' => 'Sistem & Keamanan',
            'description' => 'Daftar granular permission fitur sistem Spatie',
        ],
    ];

    /**
     * Memastikan hanya role Superuser yang dapat mengakses kontroler ini.
     */
    protected function authorizeSuperuser(): void
    {
        abort_unless(auth()->check() && auth()->user()->hasRole('Superuser'), 403, 'Akses terbatas untuk Superuser.');
    }

    /**
     * Tampilkan katalog tabel database.
     */
    public function index(): View
    {
        $this->authorizeSuperuser();

        $tableStats = [];
        foreach ($this->allowedTables as $tableName => $meta) {
            $count = DB::table($tableName)->count();
            $tableStats[$tableName] = array_merge($meta, [
                'table' => $tableName,
                'count' => $count,
            ]);
        }

        return view('superuser.database.index', [
            'tables' => $tableStats,
        ]);
    }

    /**
     * Eksplorasi isi tabel database mentah dengan pencarian dan paginasi.
     */
    public function showTable(string $table, Request $request): View
    {
        $this->authorizeSuperuser();
        abort_unless(array_key_exists($table, $this->allowedTables), 404, 'Tabel tidak diizinkan atau tidak ditemukan.');

        $columns = Schema::getColumns($table);
        $columnNames = array_column($columns, 'name');

        $query = DB::table($table);

        // Filter pencarian teks
        if ($request->filled('q')) {
            $search = $request->q;
            $textColumns = array_filter($columns, function ($col) {
                return in_array($col['type_name'], ['varchar', 'text', 'string', 'char']);
            });

            if (! empty($textColumns)) {
                $query->where(function ($q) use ($textColumns, $search) {
                    foreach ($textColumns as $index => $col) {
                        if ($index === 0) {
                            $q->where($col['name'], 'ILIKE', "%{$search}%");
                        } else {
                            $q->orWhere($col['name'], 'ILIKE', "%{$search}%");
                        }
                    }
                });
            } elseif (in_array('id', $columnNames) && is_numeric($search)) {
                $query->where('id', (int) $search);
            }
        }

        // Sorting
        $sortBy = $request->get('sort', in_array('id', $columnNames) ? 'id' : $columnNames[0]);
        $sortDir = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (in_array($sortBy, $columnNames)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $rows = $query->paginate(20)->withQueryString();
        $tableMeta = $this->allowedTables[$table];

        return view('superuser.database.table', [
            'table' => $table,
            'meta' => $tableMeta,
            'columns' => $columns,
            'rows' => $rows,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
        ]);
    }

    /**
     * Tampilkan form pembuatan baris baru di tabel.
     */
    public function createRecord(string $table): View
    {
        $this->authorizeSuperuser();
        abort_unless(array_key_exists($table, $this->allowedTables), 404, 'Tabel tidak diizinkan.');

        $columns = Schema::getColumns($table);
        $tableMeta = $this->allowedTables[$table];

        return view('superuser.database.create', [
            'table' => $table,
            'meta' => $tableMeta,
            'columns' => $columns,
        ]);
    }

    /**
     * Simpan baris baru ke tabel.
     */
    public function storeRecord(string $table, Request $request): RedirectResponse
    {
        $this->authorizeSuperuser();
        abort_unless(array_key_exists($table, $this->allowedTables), 404, 'Tabel tidak diizinkan.');

        $columns = Schema::getColumns($table);
        $data = [];

        foreach ($columns as $col) {
            $name = $col['name'];

            if ($col['auto_increment'] || in_array($name, ['id'])) {
                continue;
            }

            if (in_array($name, ['created_at', 'updated_at']) && ! $request->has($name)) {
                $data[$name] = now();

                continue;
            }

            if ($request->has($name)) {
                $val = $request->input($name);

                if ($table === 'users' && $name === 'password' && filled($val)) {
                    $val = Hash::make($val);
                } elseif ($col['type_name'] === 'bool' || $col['type'] === 'boolean') {
                    $val = $request->boolean($name);
                } elseif ($val === '' && $col['nullable']) {
                    $val = null;
                }

                $data[$name] = $val;
            } elseif ($col['nullable']) {
                $data[$name] = null;
            }
        }

        $insertedId = DB::table($table)->insertGetId($data);

        AuditLog::log(
            action: 'superuser_db_insert',
            entityType: 'RawTable:'.$table,
            entityId: $insertedId,
            newValues: $data,
            description: "Superuser menambahkan baris baru ke tabel [{$table}] ID #{$insertedId}"
        );

        return redirect()->route('superuser.database.show', $table)
            ->with('success', "Baris baru pada tabel [{$table}] berhasil dibuat dengan ID #{$insertedId}.");
    }

    /**
     * Tampilkan form editor baris data tertentu.
     */
    public function editRecord(string $table, mixed $id): View
    {
        $this->authorizeSuperuser();
        abort_unless(array_key_exists($table, $this->allowedTables), 404, 'Tabel tidak diizinkan.');

        $record = DB::table($table)->where('id', $id)->first();
        abort_unless($record, 404, 'Record dengan ID tersebut tidak ditemukan.');

        $columns = Schema::getColumns($table);
        $tableMeta = $this->allowedTables[$table];

        return view('superuser.database.edit', [
            'table' => $table,
            'meta' => $tableMeta,
            'columns' => $columns,
            'record' => (array) $record,
            'id' => $id,
        ]);
    }

    /**
     * Simpan pembaruan baris data mentah ke tabel.
     */
    public function updateRecord(string $table, mixed $id, Request $request): RedirectResponse
    {
        $this->authorizeSuperuser();
        abort_unless(array_key_exists($table, $this->allowedTables), 404, 'Tabel tidak diizinkan.');

        $oldRecord = (array) DB::table($table)->where('id', $id)->first();
        abort_unless($oldRecord, 404, 'Record tidak ditemukan.');

        $columns = Schema::getColumns($table);
        $data = [];

        foreach ($columns as $col) {
            $name = $col['name'];

            if ($col['auto_increment'] || $name === 'id') {
                continue;
            }

            if ($name === 'updated_at' && ! $request->has($name)) {
                $data[$name] = now();

                continue;
            }

            if ($request->has($name)) {
                $val = $request->input($name);

                // Khusus password users: jangan ditimpa jika dibiarkan kosong
                if ($table === 'users' && $name === 'password') {
                    if (filled($val)) {
                        $val = Hash::make($val);
                    } else {
                        continue;
                    }
                } elseif ($col['type_name'] === 'bool' || $col['type'] === 'boolean') {
                    $val = $request->boolean($name);
                } elseif ($val === '' && $col['nullable']) {
                    $val = null;
                }

                $data[$name] = $val;
            }
        }

        DB::table($table)->where('id', $id)->update($data);

        AuditLog::log(
            action: 'superuser_db_update',
            entityType: 'RawTable:'.$table,
            entityId: is_numeric($id) ? (int) $id : null,
            oldValues: $oldRecord,
            newValues: $data,
            description: "Superuser memperbarui baris data pada tabel [{$table}] ID #{$id}"
        );

        return redirect()->route('superuser.database.show', $table)
            ->with('success', "Record #{$id} pada tabel [{$table}] berhasil diperbarui.");
    }

    /**
     * Hapus baris data mentah dari tabel.
     */
    public function deleteRecord(string $table, mixed $id): RedirectResponse
    {
        $this->authorizeSuperuser();
        abort_unless(array_key_exists($table, $this->allowedTables), 404, 'Tabel tidak diizinkan.');

        $oldRecord = (array) DB::table($table)->where('id', $id)->first();
        abort_unless($oldRecord, 404, 'Record tidak ditemukan.');

        DB::table($table)->where('id', $id)->delete();

        AuditLog::log(
            action: 'superuser_db_delete',
            entityType: 'RawTable:'.$table,
            entityId: is_numeric($id) ? (int) $id : null,
            oldValues: $oldRecord,
            description: "Superuser menghapus baris data dari tabel [{$table}] ID #{$id}"
        );

        return redirect()->route('superuser.database.show', $table)
            ->with('success', "Record #{$id} pada tabel [{$table}] berhasil dihapus.");
    }
}

