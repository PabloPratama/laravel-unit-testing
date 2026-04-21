<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminEditTest extends TestCase
{
    protected function loginAsAdmin()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->join('role', 'role_user.idrole', '=', 'role.idrole')
            ->where('role.nama_role', 'Administrator')
            ->where('role_user.status', 1)
            ->select('user.*', 'role.idrole')
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

    public function test_update_rekam_medis_valid()
    {
        $this->loginAsAdmin();

        $response = $this->put('/Admin/RekamMedis/update-rekam-medis/8', [
            'anamnesa' => 'Hewan mengalami demam ringan',
            'temuan_klinis' => 'Suhu 39 derajat',
            'diagnosa' => 'Infeksi ringan',
            'detail' => 'Pemberian antibiotik injeksi',
            'idkode_tindakan_terapi' => 13 
        ]);

        $response->assertStatus(302);

        // cek rekam medis
        $this->assertDatabaseHas('rekam_medis', [
            'idrekam_medis' => 8,
            'anamnesa' => 'Hewan mengalami demam ringan'
        ]);

        // cek detail ikut keupdate
        $this->assertDatabaseHas('detail_rekam_medis', [
            'idrekam_medis' => 8,
            'detail' => 'Pemberian antibiotik injeksi'
        ]);
    }

    public function test_update_rekam_medis_kosong()
    {
        $this->loginAsAdmin();

        $response = $this->put('/Admin/RekamMedis/update-rekam-medis/8', []);

        $response->assertSessionHasErrors([
            'anamnesa',
            'temuan_klinis',
            'diagnosa',
            'detail',
            'idkode_tindakan_terapi'
        ]);
    }

    public function test_update_rekam_medis_terlalu_panjang()
    {
        $this->loginAsAdmin();

        $longText = str_repeat('a', 1001);

        $response = $this->put('/Admin/RekamMedis/update-rekam-medis/6', [
            'anamnesa' => $longText,
            'temuan_klinis' => $longText,
            'diagnosa' => $longText,
            'detail' => $longText,
            'idkode_tindakan_terapi' => 13
        ]);

        $response->assertSessionHasErrors([
            'anamnesa',
            'temuan_klinis',
            'diagnosa',
            'detail'
        ]);
    }

    public function test_update_rekam_medis_fk_invalid()
    {
        $this->loginAsAdmin();

        $response = $this->put('/Admin/RekamMedis/update-rekam-medis/5', [
            'anamnesa' => 'Update data',
            'temuan_klinis' => 'Update data',
            'diagnosa' => 'Update data',
            'detail' => 'Update data',
            'idkode_tindakan_terapi' => 999 
        ]);

        $response->assertSessionHasErrors([
            'idkode_tindakan_terapi'
        ]);
    }

    public function test_update_rekam_medis_id_tidak_ada()
    {
        $this->loginAsAdmin();

        $response = $this->put('/Admin/RekamMedis/update-rekam-medis/999', [
            'anamnesa' => 'Data',
            'temuan_klinis' => 'Data',
            'diagnosa' => 'Data',
            'detail' => 'Data',
            'idkode_tindakan_terapi' => 13
        ]);

        $response->assertStatus(404);
    }

    public function test_update_tanpa_login()
    {
        $response = $this->put('/Admin/RekamMedis/update-rekam-medis/8', [
            'anamnesa' => 'Data',
            'temuan_klinis' => 'Data',
            'diagnosa' => 'Data',
            'detail' => 'Data',
            'idkode_tindakan_terapi' => 13
        ]);

        $response->assertRedirect('/login');
    }

    public function test_update_bukan_admin()
    {
        $user = User::find(31); 

        $this->actingAs($user);
        $this->withSession(['idrole' => 5]);

        $response = $this->put('/Admin/RekamMedis/update-rekam-medis/8', [
            'anamnesa' => 'Data',
            'temuan_klinis' => 'Data',
            'diagnosa' => 'Data',
            'detail' => 'Data',
            'idkode_tindakan_terapi' => 13
        ]);

        $response->assertSessionHas('error');
    }
}