<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthProfileAccessTest extends TestCase
{
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

        DB::purge('mysql');
    }

    private function makeUser(array $overrides = []): User
    {
        $payload = array_merge([
            'name'     => 'Tester Sementara',
            'email'    => null,
            'password' => Hash::make('rahasia123'),
            'role'     => 'User',
            'bagian'   => 'PRODUKSI',
            'status'   => 'AKTIF',
            'foto'     => null,
        ], $overrides);

        return User::create($payload);
    }

    /* -------------------------------------------------------------
       LOGIN
    ------------------------------------------------------------- */

    public function test_manager_login_redirects_to_manager_dashboard(): void
    {
        $user = $this->makeUser(['role' => 'Manager', 'bagian' => 'Management']);

        $response = $this->post('/login', [
            'user_id'  => $user->id,
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('dashboard.manager'));

        $this->assertAuthenticatedAs($user);

        $user->delete();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = $this->makeUser(['status' => 'NONAKTIF']);

        $response = $this->from(route('login'))
            ->post('/login', [
                'user_id'  => $user->id,
                'password' => 'rahasia123',
            ]);

        $response->assertRedirect(route('login'));

        $response->assertSessionHasErrors('user_id');

        $this->assertGuest();

        $user->delete();
    }

    public function test_wrong_password_rejected(): void
    {
        $user = $this->makeUser();

        $response = $this->from(route('login'))
            ->post('/login', [
                'user_id'  => $user->id,
                'password' => 'password-salah',
            ]);

        $response->assertRedirect(route('login'));

        $response->assertSessionHasErrors('password');

        $this->assertGuest();

        $user->delete();
    }

    public function test_user_login_goes_to_dashboard(): void
    {
        $user = $this->makeUser();

        $response = $this->post('/login', [
            'user_id'  => $user->id,
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user->delete();
    }

    /* -------------------------------------------------------------
       PROFIL
    ------------------------------------------------------------- */

    public function test_profile_update_name_without_photo_succeeds(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->from(route('profile'))
            ->put(route('profile.update'), [
                'name' => 'Nama Baru Profil',
            ]);

        $response->assertRedirect(route('profile'));

        $response->assertSessionHas('success');

        $this->assertSame(
            'Nama Baru Profil',
            $user->fresh()->name
        );

        $user->delete();
    }

    public function test_profile_update_requires_name(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->from(route('profile'))
            ->put(route('profile.update'), [
                'name' => '',
            ]);

        $response->assertSessionHasErrors('name');

        $user->delete();
    }

    public function test_profile_photo_upload_works(): void
    {
        Storage::fake('public');

        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );

        $file = File::fake()->createWithContent('avatar.png', $png);

        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->from(route('profile.photo'))
            ->post(route('profile.photo.update'), [
                'foto' => $file,
            ]);

        $response->assertRedirect(route('profile.photo'));

        $response->assertSessionHas('success');

        $fresh = $user->fresh();

        $this->assertNotNull($fresh->foto);

        $this->assertTrue(
            Storage::disk('public')->exists($fresh->foto),
            'File foto tersimpan di storage public.'
        );

        $user->delete();
    }

    /* -------------------------------------------------------------
       HAK AKSES TAMBAHAN
    ------------------------------------------------------------- */

    public function test_maintenance_cannot_open_manager_dashboard_and_settings(): void
    {
        $user = $this->makeUser([
            'role'   => 'Maintenance',
            'bagian' => 'Maintenance',
        ]);

        $this->actingAs($user)
            ->get('/dashboard/manager')
            ->assertForbidden();

        $this->actingAs($user)
            ->get('/pengaturan')
            ->assertForbidden();

        $user->delete();
    }

    public function test_regular_user_blocked_from_master_module(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->get('/areas')
            ->assertForbidden();

        $this->actingAs($user)
            ->get('/machines')
            ->assertForbidden();

        $user->delete();
    }
}
