<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminLihatTDTest extends TestCase
{
    protected function loginAsAdmin()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->join('role', 'role_user.idrole', '=', 'role.idrole')
            ->where('role.nama_role', 'Administrator')
            ->where('role_user.status', 1)
            ->select('user.*')
            ->first();

        $this->actingAs(User::find($user->iduser));

        $this->withSession([
            'idrole' => 1,
            'iduser' => $user->iduser
        ]);
    }

    public function test_lihat_data_hari_ini()
    {
        $this->loginAsAdmin();

        $response = $this->get('/Admin/TemuDokter/daftar-temu-dokter');

        $response->assertStatus(200);
        $response->assertViewHas('temuDokterlist');
    }

    public function test_filter_dokter_valid()
    {
        $this->loginAsAdmin();

        $dokter = DB::table('role_user')
            ->where('idrole_user', 41) 
            ->where('idrole', 2)
            ->where('status', 1)
            ->first();

        $this->assertNotNull($dokter);

        $response = $this->get('/Admin/TemuDokter/daftar-temu-dokter?filter_dokter=' . $dokter->idrole_user);

        $response->assertStatus(200);
        $response->assertViewHas('temuDokterlist');
    }

    public function test_filter_dokter_tidak_ada()
    {
        $this->loginAsAdmin();

        $response = $this->get('/Admin/TemuDokter/daftar-temu-dokter?filter_dokter=99999');

        $response->assertStatus(200);
        $response->assertViewHas('temuDokterlist', function ($data) {
            return $data->count() === 0;
        });
    }

    public function test_data_soft_delete_tidak_muncul()
    {
        $this->loginAsAdmin();

        $response = $this->get('/Admin/TemuDokter/daftar-temu-dokter');

        $response->assertStatus(200);

        $response->assertViewHas('temuDokterlist', function ($data) {
            return $data->where('deleted_at', '!=', null)->count() === 0;
        });
    }
}