<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PerawatLihatTest extends TestCase
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
            $this->markTestSkipped('User perawat tidak ditemukan di database');
        }

        $userModel = User::where('iduser', $user->iduser)->first();

        $this->actingAs($userModel);

        $this->withSession([
            'idrole' => $user->idrole
        ]);
    }

    public function test_perawat_lihat_rekam_medis_valid()
    {
        $this->loginAsPerawat();

        $response = $this->get('/Perawat/RekamMedis/detail-rekam-medis/8');

        $response->assertStatus(200);

        $response->assertSee('asdasdadds');
        $response->assertSee('aku adalah');
    }

    public function test_perawat_lihat_rekam_medis_tidak_ditemukan()
    {
        $this->loginAsPerawat();

        $response = $this->get('/Perawat/RekamMedis/detail-rekam-medis/999');

        $response->assertStatus(404);
    }

    public function test_perawat_lihat_rekam_medis_relasi()
    {
        $this->loginAsPerawat();

        $response = $this->get('/Perawat/RekamMedis/detail-rekam-medis/8');

        $response->assertStatus(200);

        $response->assertSee('Ica'); 
        $response->assertSee('Hyacine'); 
        $response->assertSee('Vaksinasi lainnya'); 
    }
}