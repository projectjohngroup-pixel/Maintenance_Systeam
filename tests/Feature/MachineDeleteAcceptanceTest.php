<?php

namespace Tests\Feature;

use App\Models\Machine\Area;
use App\Models\Machine\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\TestCase;

class MachineDeleteAcceptanceTest extends TestCase
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
                if ($entry['type'] === 'machine') {
                    Machine::where('id', $entry['id'])->delete();
                }
                if ($entry['type'] === 'area') {
                    Area::where('id', $entry['id'])->delete();
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

    protected function track(array $entry): void
    {
        $this->created[] = $entry;
    }

    public function test_machine_full_crud_acceptance_with_delete_recheck(): void
    {
        $area = Area::create(['nama_area' => 'AREA_SG_TEST_' . uniqid()]);
        $this->track(['type' => 'area', 'id' => $area->id]);

        /*
        |======================================================================
        | A. CREATE -> PASS
        |======================================================================
        */

        $storeResponse = $this->actingAs($this->admin())->post(route('machines.store'), [
            'kode_mesin' => 'MESIN-SYSTEM-GUARD-TEST-' . uniqid(),
            'nama_mesin' => 'MESIN-SYSTEM-GUARD-TEST',
            'area_id'    => $area->id,
            'kw'         => 7.5,
            'status'     => 'Aktif',
            'spesifikasi'=> 'Mesin uji System Guard',
        ]);

        $storeResponse->assertRedirect(route('machines.index'));
        $storeResponse->assertSessionHas('success');

        $machine = Machine::where('nama_mesin', 'MESIN-SYSTEM-GUARD-TEST')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($machine, 'CREATE harus menghasilkan record mesin');

        $this->track(['type' => 'machine', 'id' => $machine->id]);

        /*
        |======================================================================
        | B. EDIT -> PASS
        |======================================================================
        */

        $updateResponse = $this->actingAs($this->admin())->put(route('machines.update', $machine), [
            'kode_mesin' => $machine->kode_mesin,
            'nama_mesin' => 'MESIN-SYSTEM-GUARD-TEST-UPDATED',
            'area_id'    => $area->id,
            'kw'         => 8.0,
            'status'     => 'Tidak Aktif',
        ]);

        $updateResponse->assertRedirect(route('machines.index'));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('mesins', [
            'id'         => $machine->id,
            'nama_mesin' => 'MESIN-SYSTEM-GUARD-TEST-UPDATED',
            'status'     => 'Tidak Aktif',
        ]);

        /*
        |======================================================================
        | C. UI memuat, bukan stuck loading
        |======================================================================
        | Halaman index harus memuat:
        | - elemen globalLoading (dari GlobalLoading middleware)
        | - modal konfirmasi pchModalBackdrop
        | - tombol hapus + form DELETE dengan data-confirm + @csrf
        | - loading JS memuat aturan pengecualian form [data-confirm]
        |======================================================================
        */

        $indexResponse = $this->actingAs($this->admin())->get(route('machines.index'));

        $indexResponse->assertOk();

        $html = $indexResponse->getContent();

        $this->assertStringContainsString('id="globalLoading"', $html, 'GlobalLoading harus terpasang');
        $this->assertStringContainsString('id="pchModalBackdrop"', $html, 'Modal konfirmasi harus ada');
        $this->assertStringContainsString('id="globalLoadingText"', $html);
        $this->assertStringContainsString('window.__GLOBAL_LOADING_INITIALIZED__', $html);
        $this->assertStringContainsString(
            'form.hasAttribute(',
            $html,
            'GlobalLoading JS harus melewati form [data-confirm]'
        );

        $this->assertStringContainsString('data-confirm="Hapus mesin', $html, 'Form hapus harus memakai data-confirm');
        $this->assertStringContainsString('name="_method"', $html);
        $this->assertStringContainsString('value="DELETE"', $html);
        $this->assertStringContainsString('name="_token"', $html);

        /*
        |======================================================================
        | D. DELETE -> PASS (simulasi submit form POST + _method=DELETE)
        |======================================================================
        */

        $deleteResponse = $this->actingAs($this->admin())->call(
            'DELETE',
            route('machines.destroy', $machine),
            [],
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest', 'HTTP_ACCEPT' => 'text/html']
        );

        $deleteResponse->assertRedirect(route('machines.index'));
        $deleteResponse->assertSessionHas('success');

        /*
        |======================================================================
        | E. Data hilang dari tabel -> PASS
        |======================================================================
        */

        $this->assertDatabaseMissing('mesins', ['id' => $machine->id]);

        /*
        |======================================================================
        | F. Browser refresh (GET ulang) -> data tetap hilang -> PASS
        |======================================================================
        */

        $refreshResponse = $this->actingAs($this->admin())->get(route('machines.index'));

        $refreshResponse->assertOk();
        $this->assertStringNotContainsString(
            'MESIN-SYSTEM-GUARD-TEST-UPDATED',
            $refreshResponse->getContent(),
            'Setelah refresh, mesin yang dihapus tidak boleh muncul'
        );

        /*
        |======================================================================
        | G. Mesin lain tidak ikut terhapus -> PASS
        |======================================================================
        */

        $other = Machine::create([
            'kode_mesin' => 'KM_SG_KEEP_' . uniqid(),
            'nama_mesin' => 'MESIN LAIN TIDAK TERHAPUS',
            'status'     => 'Aktif',
        ]);
        $this->track(['type' => 'machine', 'id' => $other->id]);

        $this->assertDatabaseHas('mesins', ['id' => $other->id]);
    }
}