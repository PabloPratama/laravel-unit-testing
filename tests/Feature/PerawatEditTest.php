<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PerawatEditTest extends TestCase
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
            'idrole' => 3
        ]);
    }

    public function test_edit_rekam_medis_valid()
    {
        $this->loginAsPerawat();

        $response = $this->put('/Perawat/RekamMedis/update-rekam-medis/8', [
            'anamnesa' => 'asdasdadds update',
            'temuan_klinis' => 'aku adalah update',
            'diagnosa' => 'asdasdadasdsa update',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('rekam_medis', [
            'idrekam_medis' => 8,
            'anamnesa' => 'asdasdadds update',
            'temuan_klinis' => 'aku adalah update',
            'diagnosa' => 'asdasdadasdsa update',
        ]);
    }

    public function test_edit_rekam_medis_kosong()
    {
        $this->loginAsPerawat();

        $response = $this->from('/Perawat/RekamMedis/detail-rekam-medis/8')
            ->put('/Perawat/RekamMedis/update-rekam-medis/8', [
                'anamnesa' => null,
                'temuan_klinis' => null,
                'diagnosa' => null,
            ]);

        $response->assertSessionHasErrors([
            'anamnesa',
            'temuan_klinis',
            'diagnosa'
        ]);
    }

    public function test_edit_rekam_medis_terlalu_panjang()
    {
        $this->loginAsPerawat();

        $longText = str_repeat('a', 1001);

        $response = $this->from('/Perawat/RekamMedis/detail-rekam-medis/8')
            ->put('/Perawat/RekamMedis/update-rekam-medis/8', [
                'anamnesa' => $longText,
                'temuan_klinis' => $longText,
                'diagnosa' => $longText,
            ]);

        $response->assertSessionHasErrors([
            'anamnesa',
            'temuan_klinis',
            'diagnosa'
        ]);
    }

}