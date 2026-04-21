<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PerawatHapusTest extends TestCase
{
    protected function loginAsPerawat()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->join('role', 'role_user.idrole', '=', 'role.idrole')
            ->where('role.nama_role', 'Perawat')
            ->where('role_user.status', 1)
            ->select('user.*', 'role.idrole')
            ->first();

        if (!$user) {
            $this->markTestSkipped('User perawat tidak ditemukan');
        }

        $userModel = User::where('iduser', $user->iduser)->first();

        $this->actingAs($userModel);

        $this->withSession([
            'idrole' => 3,
            'iduser' => $user->iduser
        ]);
    }

    public function test_hapus_rekam_medis_valid()
    {
        $this->loginAsPerawat();

        $response = $this->delete('/Perawat/RekamMedis/delete-rekam-medis/8');

        $response->assertStatus(302);

        // cek rekam medis soft delete
        $this->assertDatabaseHas('rekam_medis', [
            'idrekam_medis' => 8,
        ]);

        $this->assertNotNull(
            DB::table('rekam_medis')->where('idrekam_medis', 8)->value('deleted_at')
        );

        // cek detail ikut kehapus (relasi)
        $this->assertNotNull(
            DB::table('detail_rekam_medis')->where('idrekam_medis', 8)->value('deleted_at')
        );

        // cek status temu_dokter balik ke W
        $this->assertEquals(
            'W',
            DB::table('temu_dokter')
                ->join('rekam_medis', 'temu_dokter.idreservasi_dokter', '=', 'rekam_medis.idreservasi_dokter')
                ->where('rekam_medis.idrekam_medis', 8)
                ->value('status')
        );
    }

    public function test_hapus_rekam_medis_tidak_ditemukan()
    {
        $this->loginAsPerawat();

        $response = $this->delete('/Perawat/RekamMedis/delete-rekam-medis/999');

        $response->assertStatus(302);
    }

    public function test_hapus_rekam_medis_tanpa_login()
    {
        $response = $this->delete('/Perawat/RekamMedis/delete-rekam-medis/6');

        $response->assertRedirect('/login');
    }

    public function test_hapus_rekam_medis_bukan_perawat()
    {
        $user = User::where('iduser', 31)->first(); 

        $this->actingAs($user);

        $this->withSession([
            'idrole' => 5 
        ]);

        $response = $this->delete('/Perawat/RekamMedis/delete-rekam-medis/6');

        $response->assertSessionHas('error');
    }
}