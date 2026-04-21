<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminTambahTDTest extends TestCase
{
    protected function loginAsAdmin()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->where('role_user.idrole', 1) 
            ->where('role_user.status', 1)
            ->select('user.*')
            ->first();

        if (!$user) {
            $this->markTestSkipped('Admin tidak ditemukan');
        }

        $this->actingAs(User::find($user->iduser));

        $this->withSession([
            'idrole' => 1,
            'iduser' => $user->iduser
        ]);
    }

    public function test_admin_tambah_reservasi_valid()
    {
        $this->loginAsAdmin();

        $pet = DB::table('pet')->whereNull('deleted_at')->first();
        $dokter = DB::table('role_user')
            ->where('idrole', 2)
            ->where('status', 1)
            ->first();

        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('temu_dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user,
            'status' => 'W'
        ]);
    }

    public function test_admin_tambah_kosong()
    {
        $this->loginAsAdmin();

        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', []);

        $response->assertSessionHasErrors([
            'idpet',
            'idrole_user'
        ]);
    }

    public function test_admin_tambah_id_tidak_ada()
    {
        $this->loginAsAdmin();

        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', [
            'idpet' => 999,
            'idrole_user' => 999
        ]);

        $response->assertSessionHasErrors([
            'idpet',
            'idrole_user'
        ]);
    }

    public function test_admin_redirect_setelah_tambah()
    {
        $this->loginAsAdmin();

        $pet = DB::table('pet')->first();
        $dokter = DB::table('role_user')->where('idrole', 2)->first();

        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);

        $response->assertRedirect(
            route('Admin.TemuDokter.daftar-temu-dokter')
        );
    }

    public function test_admin_tambah_tanpa_login()
    {
        $pet = DB::table('pet')->first();
        $dokter = DB::table('role_user')->where('idrole', 2)->first();

        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);

        $response->assertRedirect('/login');
    }

    public function test_admin_tambah_bukan_admin()
    {
        $user = User::find(31); 

        $this->actingAs($user);

        $this->withSession([
            'idrole' => 5
        ]);

        $pet = DB::table('pet')->first();
        $dokter = DB::table('role_user')->where('idrole', 2)->first();

        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);

        $response->assertSessionHas('error');
    }
}