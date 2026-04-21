<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PerawatTambahTest extends TestCase
{
    protected function loginAsPerawat()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->join('role', 'role_user.idrole', '=', 'role.idrole')
            ->where('role.nama_role', 'Perawat')
            ->select('user.*')
            ->first();

        if (!$user) {
            $this->markTestSkipped('User perawat tidak ditemukan di database');
        }

        $userModel = User::where('iduser', $user->iduser)->first();

        $this->actingAs($userModel);
    }

    public function test_perawat_tambah_rekam_medis_valid()
    {
        $this->loginAsPerawat();

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 17,
            'anamnesa' => 'Sakit ringan',
            'temuan_klinis' => 'Demam',
            'diagnosa' => 'Flu',
            'detail' => 'Diberi obat',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('rekam_medis', [
            'anamnesa' => 'Sakit ringan'
        ]);
    }

    public function test_perawat_tambah_field_kosong()
    {
        $this->loginAsPerawat();

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 17,
            'anamnesa' => null,
            'temuan_klinis' => 'Demam',
            'diagnosa' => 'Flu',
            'detail' => 'Test',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertStatus(302);
    }

    public function test_perawat_input_terlalu_panjang()
    {
        $this->loginAsPerawat();

        $longText = str_repeat('a', 1001);

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 17,
            'anamnesa' => $longText,
            'temuan_klinis' => 'Test',
            'diagnosa' => 'Test',
            'detail' => 'Test',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertStatus(302);
    }

    public function test_perawat_id_reservasi_valid()
    {
        $this->loginAsPerawat();

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 17,
            'anamnesa' => 'Valid Relasi',
            'temuan_klinis' => 'Normal',
            'diagnosa' => 'Sehat',
            'detail' => 'Test',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('rekam_medis', [
            'anamnesa' => 'Valid Relasi'
        ]);
    }

    public function test_perawat_id_reservasi_tidak_valid()
    {
        $this->loginAsPerawat();

        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 999,
            'anamnesa' => 'Test',
            'temuan_klinis' => 'Test',
            'diagnosa' => 'Test',
            'detail' => 'Test',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertStatus(302);
    }
}