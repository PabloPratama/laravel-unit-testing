<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminHapusTest extends TestCase
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

    public function test_hapus_rekam_medis_valid()
    {
        $this->loginAsAdmin();

        $response = $this->delete('/Admin/RekamMedis/delete-rekam-medis/8');

        $response->assertStatus(302);

        // rekam medis soft delete
        $this->assertNotNull(
            DB::table('rekam_medis')
                ->where('idrekam_medis', 8)
                ->value('deleted_at')
        );

        // detail ikut ke-delete
        $this->assertNotNull(
            DB::table('detail_rekam_medis')
                ->where('idrekam_medis', 8)
                ->value('deleted_at')
        );

        // status temu dokter balik ke W
        $this->assertEquals(
            'W',
            DB::table('temu_dokter')
                ->where('idreservasi_dokter', 29)
                ->value('status')
        );
    }

    public function test_hapus_rekam_medis_tidak_ada()
    {
        $this->loginAsAdmin();

        $response = $this->delete('/Admin/RekamMedis/delete-rekam-medis/999');

        $response->assertStatus(404);
    }

    public function test_hapus_tanpa_login()
    {
        $response = $this->delete('/Admin/RekamMedis/delete-rekam-medis/6');

        $response->assertRedirect('/login');
    }

    public function test_hapus_bukan_admin()
    {
        $user = User::find(31); 

        $this->actingAs($user);
        $this->withSession([
            'idrole' => 5
        ]);

        $response = $this->delete('/Admin/RekamMedis/delete-rekam-medis/6');

        $response->assertSessionHas('error');
    }

}