<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ResepsionisTambahTest extends TestCase
{
    protected function loginAsResepsionis()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->join('role', 'role_user.idrole', '=', 'role.idrole')
            ->where('role.nama_role', 'Resepsionis')
            ->where('role_user.status', 1)
            ->select('user.*', 'role_user.idrole_user')
            ->first();

        if (!$user) {
            $this->markTestSkipped('Resepsionis tidak ditemukan');
        }

        $this->actingAs(User::find($user->iduser));

        $this->withSession([
            'idrole' => 4,
            'iduser' => $user->iduser
        ]);
    }

    public function test_tambah_reservasi_valid()
    {
        $this->loginAsResepsionis();

        $pet = DB::table('pet')->first();
        $dokter = DB::table('role_user')
            ->where('idrole', 2) 
            ->where('status', 1)
            ->first();

        $response = $this->post('/Resepsionis/TemuDokter/store-temu-dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('temu_dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);
    }

    public function test_tambah_reservasi_kosong()
    {
        $this->loginAsResepsionis();

        $response = $this->post('/Resepsionis/TemuDokter/store-temu-dokter', []);

        $response->assertSessionHasErrors([
            'idpet',
            'idrole_user'
        ]);
    }


    public function test_tambah_reservasi_fk_invalid()
    {
        $this->loginAsResepsionis();

        $response = $this->post('/Resepsionis/TemuDokter/store-temu-dokter', [
            'idpet' => 99999,
            'idrole_user' => 99999
        ]);

        $response->assertSessionHasErrors([
            'idpet',
            'idrole_user'
        ]);
    }

    public function test_flow_controller()
    {
        $this->loginAsResepsionis();

        $pet = DB::table('pet')->first();
        $dokter = DB::table('role_user')
            ->where('idrole', 2)
            ->where('status', 1)
            ->first();

        $response = $this->post('/Resepsionis/TemuDokter/store-temu-dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);

        $response->assertRedirect(
            route('Resepsionis.TemuDokter.daftar-temu-dokter')
        );
    }

    public function test_tambah_tanpa_login()
    {
        $pet = DB::table('pet')->first();
        $dokter = DB::table('role_user')->where('idrole', 2)->first();

        $response = $this->post('/Resepsionis/TemuDokter/store-temu-dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);

        $response->assertRedirect('/login');
    }

    public function test_tambah_bukan_resepsionis()
    {
        $user = User::find(31); 

        $this->actingAs($user);

        $this->withSession([
            'idrole' => 5
        ]);

        $pet = DB::table('pet')->first();
        $dokter = DB::table('role_user')->where('idrole', 2)->first();

        $response = $this->post('/Resepsionis/TemuDokter/store-temu-dokter', [
            'idpet' => $pet->idpet,
            'idrole_user' => $dokter->idrole_user
        ]);

        $response->assertSessionHas('error');
    }
}