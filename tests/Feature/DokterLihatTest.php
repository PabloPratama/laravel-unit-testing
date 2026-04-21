<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DokterLihatTest extends TestCase
{
    protected function loginAsDokter()
    {
        $roleUser = DB::table('role_user')
            ->where('idrole_user', 53)
            ->first();

        $user = User::find($roleUser->iduser);

        $this->actingAs($user);

        $this->withSession([
            'idrole' => 2,
            'iduser' => $roleUser->iduser,
            'idrole_user' => 53
        ]);
    }

    public function test_dokter_lihat_rekam_medis_valid()
    {
        $this->loginAsDokter();

        $response = $this->get('/Dokter/RekamMedis/detail-rekam-medis/5');

        $response->assertStatus(200);

        $response->assertSee('Relasi valid update');
        $response->assertSee('Normal');
        $response->assertSee('Sehat');
    }

    public function test_dokter_lihat_rekam_medis_tidak_ditemukan()
    {
        $this->loginAsDokter();

        $response = $this->get('/Dokter/RekamMedis/detail-rekam-medis/999');

        $response->assertStatus(404);
    }

    public function test_dokter_lihat_rekam_medis_relasi()
    {
        $this->loginAsDokter();

        $response = $this->get('/Dokter/RekamMedis/detail-rekam-medis/5');

        $response->assertStatus(200);
        $response->assertSee('Ica');
        $response->assertSee('Hyacine');
        $response->assertSee('Vitamin / suplemen');
    }

    public function test_dokter_lihat_tanpa_login()
    {
        $response = $this->get('/Dokter/RekamMedis/detail-rekam-medis/5');

        $response->assertRedirect('/login');
    }

    public function test_dokter_lihat_bukan_dokter()
    {
        $user = User::find(31); 

        $this->actingAs($user);

        $this->withSession([
            'idrole' => 5
        ]);

        $response = $this->get('/Dokter/RekamMedis/detail-rekam-medis/5');

        $response->assertSessionHas('error');
    }
}