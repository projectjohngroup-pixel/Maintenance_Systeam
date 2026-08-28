<?php

namespace Tests\Feature;

use App\Models\Inventory\Barang;
use App\Models\Inventory\BarangMasuk;
use App\Models\Inventory\Satuan;
use App\Models\Machine\Area;
use App\Models\Machine\Machine;
use App\Models\Machine\MachineSparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\TestCase;

class DeleteFlowTest extends TestCase
{
    protected array $created = [];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default'                    => 'mysql',
            'database.connections.mysql.database' => 'Maintenance_Systeam',
            'database.connections.mysql.host'     => env('DB_HOST', '127.0.0.1'),
            'database.connections.mysql.port'     => env('DB_PORT', '3306'),
            'database.connections.mysql.username' => env('DB_USERNAME', 'root'),
            'database.connections.mysql.password' => env('DB_PASSWORD', ''),
            'session.driver'                      => 'array',
            'cache.default'                       => 'array',
        ]);

        \Illuminate\Support\Facades\DB::purge('mysql');
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->created) as $entry) {
            try {
                if ($entry['type'] === 'area') {
                    \App\Models\Machine\Area::where('id', $entry['id'])->delete();
                }
                if ($entry['type'] === 'machine') {
                    \App\Models\Machine\Machine::where('id', $entry['id'])->delete();
                }
                if ($entry['type'] === 'sparepart') {
                    \App\Models\Machine\MachineSparepart::where('id', $entry['id'])->delete();
                }
                if ($entry['type'] === 'satuan') {
                    \App\Models\Inventory\Satuan::where('id', $entry['id'])->delete();
                }
                if ($entry['type'] === 'barang') {
                    \App\Models\Inventory\Barang::where('id', $entry['id'])->delete();
                }
                if ($entry['type'] === 'masuk') {
                    \App\Models\Inventory\BarangMasuk::where('id', $entry['id'])->delete();
                }
            } catch (\Throwable $e) {
                // ignore on cleanup
            }
        }
        parent::tearDown();
    }

    protected function admin(): User
    {
        $user = User::where('role', 'Administrator')->where('status', 'AKTIF')->first();
        $this->assertNotNull($user, 'Administrator user not found');
        return $user;
    }

    protected function unique(string $prefix): string
    {
        return $prefix . '_del_test_' . uniqid();
    }

    public function test_delete_area_with_no_machines_succeeds(): void
    {
        $area = Area::create(['nama_area' => $this->unique('AREA')]);
        $this->created[] = ['type' => 'area', 'id' => $area->id];

        $response = $this->actingAs($this->admin())->delete(route('areas.destroy', $area));

        $response->assertRedirect(route('areas.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('areas', ['id' => $area->id]);
    }

    public function test_delete_area_with_machines_is_blocked(): void
    {
        $area = Area::create(['nama_area' => $this->unique('AREA')]);
        $machine = Machine::create([
            'kode_mesin' => $this->unique('KM'),
            'nama_mesin' => $this->unique('MESIN'),
            'area_id'    => $area->id,
            'status'     => 'Aktif',
        ]);
        $this->created[] = ['type' => 'area', 'id' => $area->id];
        $this->created[] = ['type' => 'machine', 'id' => $machine->id];

        $response = $this->actingAs($this->admin())->delete(route('areas.destroy', $area));

        $response->assertRedirect();
        $response->assertSessionHasErrors('area');
        $this->assertDatabaseHas('areas', ['id' => $area->id]);
    }

    public function test_delete_machine_succeeds_and_cascades_spareparts(): void
    {
        $machine = Machine::create([
            'kode_mesin' => $this->unique('KM'),
            'nama_mesin' => $this->unique('MESIN'),
            'status'     => 'Aktif',
        ]);
        $satuan = Satuan::create(['nama' => $this->unique('SAT')]);
        $barang = Barang::create([
            'kode_barang'      => $this->unique('KB'),
            'nama_spesifikasi' => $this->unique('BARANG'),
            'satuan_id'        => $satuan->id,
            'stok'             => 10,
            'stok_minimum'     => 1,
        ]);
        $sparepart = MachineSparepart::create([
            'machine_id' => $machine->id,
            'barang_id'  => $barang->id,
            'qty'        => 2,
        ]);
        $this->created[] = ['type' => 'machine', 'id' => $machine->id];
        $this->created[] = ['type' => 'sparepart', 'id' => $sparepart->id];
        $this->created[] = ['type' => 'barang', 'id' => $barang->id];
        $this->created[] = ['type' => 'satuan', 'id' => $satuan->id];

        $response = $this->actingAs($this->admin())->delete(route('machines.destroy', $machine));

        $response->assertRedirect(route('machines.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('mesins', ['id' => $machine->id]);
    }

    public function test_delete_satuan_used_by_barang_is_blocked(): void
    {
        $satuan = Satuan::create(['nama' => $this->unique('SAT')]);
        $barang = Barang::create([
            'kode_barang'      => $this->unique('KB'),
            'nama_spesifikasi' => $this->unique('BARANG'),
            'satuan_id'        => $satuan->id,
            'stok'             => 5,
            'stok_minimum'     => 1,
        ]);
        $this->created[] = ['type' => 'satuan', 'id' => $satuan->id];
        $this->created[] = ['type' => 'barang', 'id' => $barang->id];

        $response = $this->actingAs($this->admin())->delete(route('satuan.destroy', $satuan));

        $response->assertRedirect();
        $response->assertSessionHasErrors('satuan');
        $this->assertDatabaseHas('satuans', ['id' => $satuan->id]);
    }

    public function test_delete_satuan_with_no_relations_succeeds(): void
    {
        $satuan = Satuan::create(['nama' => $this->unique('SAT')]);
        $this->created[] = ['type' => 'satuan', 'id' => $satuan->id];

        $response = $this->actingAs($this->admin())->delete(route('satuan.destroy', $satuan));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('satuans', ['id' => $satuan->id]);
    }

    public function test_delete_barang_with_no_transactions_succeeds(): void
    {
        $barang = Barang::create([
            'kode_barang'      => $this->unique('KB'),
            'nama_spesifikasi' => $this->unique('BARANG'),
            'stok'             => 0,
            'stok_minimum'     => 1,
        ]);
        $this->created[] = ['type' => 'barang', 'id' => $barang->id];

        $response = $this->actingAs($this->admin())->delete(route('barang.destroy', $barang));

        $response->assertRedirect(route('barang.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('barangs', ['id' => $barang->id]);
    }

    public function test_delete_barang_with_barang_masuk_transaction_is_blocked(): void
    {
        $satuan = Satuan::create(['nama' => $this->unique('SAT')]);
        $barang = Barang::create([
            'kode_barang'      => $this->unique('KB'),
            'nama_spesifikasi' => $this->unique('BARANG'),
            'satuan_id'        => $satuan->id,
            'stok'             => 10,
            'stok_minimum'     => 1,
        ]);
        $masuk = BarangMasuk::create([
            'no_transaksi' => $this->unique('BM'),
            'tanggal_masuk' => now()->toDateString(),
            'barang_id'     => $barang->id,
            'qty'           => 5,
            'satuan_id'     => $satuan->id,
            'status'        => 'RECEIVED',
        ]);
        $this->created[] = ['type' => 'satuan', 'id' => $satuan->id];
        $this->created[] = ['type' => 'barang', 'id' => $barang->id];
        $this->created[] = ['type' => 'masuk', 'id' => $masuk->id];

        $response = $this->actingAs($this->admin())->delete(route('barang.destroy', $barang));

        $response->assertRedirect(route('barang.index'));
        $response->assertSessionHasErrors('barang');
        $this->assertDatabaseHas('barangs', ['id' => $barang->id]);
    }

    public function test_delete_work_order_invalid_id_returns_404(): void
    {
        $response = $this->actingAs($this->admin())->delete(route('work-orders.admin.destroy', 99999999));
        $response->assertNotFound();
    }

    public function test_delete_barang_invalid_id_returns_404(): void
    {
        $response = $this->actingAs($this->admin())->delete(route('barang.destroy', 99999999));
        $response->assertNotFound();
    }

    public function test_unauthorized_user_cannot_delete(): void
    {
        $barang = Barang::create([
            'kode_barang'      => $this->unique('KB'),
            'nama_spesifikasi' => $this->unique('BARANG'),
            'stok'             => 0,
            'stok_minimum'     => 1,
        ]);
        $this->created[] = ['type' => 'barang', 'id' => $barang->id];

        $limited = User::where('role', 'User')->where('status', 'AKTIF')->first();
        $this->assertNotNull($limited, 'User role not found');

        // User role cannot reach the barang delete route (403 from middleware)
        $response = $this->actingAs($limited)->delete(route('barang.destroy', $barang));
        $response->assertForbidden();
        $this->assertDatabaseHas('barangs', ['id' => $barang->id]);
    }
}
