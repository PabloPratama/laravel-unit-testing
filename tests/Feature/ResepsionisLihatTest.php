<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ResepsionisLihatTest extends TestCase
{
    protected function loginAsResepsionis()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->join('role', 'role_user.idrole', '=', 'role.idrole')
            ->where('role.nama_role', 'Resepsionis')
            ->where('role_user.status', 1)
            ->select('user.*')
            ->first();

        $this->actingAs(User::find($user->iduser));

        $this->withSession([
            'idrole' => 4,
            'iduser' => $user->iduser
        ]);
    }

    public function test_lihat_data_hari_ini()
    {
        $this->loginAsResepsionis();

        $response = $this->get('/Resepsionis/TemuDokter/daftar-temu-dokter');

        $response->assertStatus(200);
        $response->assertViewHas('temuDokterlist');
    }

    public function test_filter_dokter_valid()
    {
    $this->loginAsResepsionis();

    $dokter = DB::table('role_user')
        ->where('idrole_user', 41)
        ->where('idrole', 2)
        ->where('status', 1)
        ->first();

    $this->assertNotNull($dokter); 
    $response = $this->get('/Resepsionis/TemuDokter/daftar-temu-dokter?filter_dokter=' . $dokter->idrole_user);
    $response->assertStatus(200);
    $response->assertViewHas('temuDokterlist');
    }

    public function test_filter_dokter_tidak_ada()
    {
        $this->loginAsResepsionis();

        $response = $this->get('/Resepsionis/TemuDokter/daftar-temu-dokter?filter_dokter=99999');

        $response->assertStatus(200);
        $response->assertViewHas('temuDokterlist', function ($data) {
            return $data->count() === 0;
        });
    }

    public function test_data_soft_delete_tidak_muncul()
    {
        $this->loginAsResepsionis();

        $response = $this->get('/Resepsionis/TemuDokter/daftar-temu-dokter');

        $response->assertStatus(200);

        $response->assertViewHas('temuDokterlist', function ($data) {
            return $data->where('deleted_at', '!=', null)->count() === 0;
        });
    }
}