<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminTambahTest extends TestCase
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

    public function test_tambah_rekam_medis_valid()
    {
        $this->loginAsAdmin();

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 18,
            'anamnesa' => 'Hewan demam',
            'temuan_klinis' => 'Suhu tinggi',
            'diagnosa' => 'Infeksi ringan',
            'detail' => 'Antibiotik injeksi',
            'idkode_tindakan_terapi' => 13
        ]);

        $response->assertStatus(302);

        // rekam medis
        $this->assertDatabaseHas('rekam_medis', [
            'idreservasi_dokter' => 18,
            'anamnesa' => 'Hewan demam'
        ]);

        // detail
        $this->assertDatabaseHas('detail_rekam_medis', [
            'detail' => 'Antibiotik injeksi'
        ]);

        // status berubah
        $this->assertEquals(
            'D',
            DB::table('temu_dokter')
                ->where('idreservasi_dokter', 18)
                ->value('status')
        );
    }

    public function test_tambah_rekam_medis_kosong()
    {
        $this->loginAsAdmin();

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', []);

        $response->assertSessionHasErrors([
            'idreservasi_dokter',
            'anamnesa',
            'temuan_klinis',
            'diagnosa',
            'detail',
            'idkode_tindakan_terapi'
        ]);
    }

    public function test_tambah_rekam_medis_terlalu_panjang()
    {
        $this->loginAsAdmin();

        $longText = str_repeat('a', 1001);

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 18,
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

    public function test_tambah_rekam_medis_fk_invalid()
    {
        $this->loginAsAdmin();

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 999,
            'anamnesa' => 'Data',
            'temuan_klinis' => 'Data',
            'diagnosa' => 'Data',
            'detail' => 'Data',
            'idkode_tindakan_terapi' => 999
        ]);

        $response->assertSessionHasErrors([
            'idreservasi_dokter',
            'idkode_tindakan_terapi'
        ]);
    }

    public function test_tambah_tanpa_login()
    {
        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 18,
            'anamnesa' => 'Test',
            'temuan_klinis' => 'Test',
            'diagnosa' => 'Test',
            'detail' => 'Test',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertRedirect('/login');
    }

    public function test_tambah_bukan_admin()
    {
        $user = User::find(31);

        $this->actingAs($user);

        $this->withSession([
            'idrole' => 5
        ]);

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 18,
            'anamnesa' => 'Test',
            'temuan_klinis' => 'Test',
            'diagnosa' => 'Test',
            'detail' => 'Test',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertSessionHas('error');
    }
}