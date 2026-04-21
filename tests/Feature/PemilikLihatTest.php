<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PemilikLihatTest extends TestCase
{
    protected function loginAsPemilik()
    {
        $roleUser = DB::table('role_user')
            ->where('idrole_user', 44) 
            ->first();

        $user = User::find($roleUser->iduser);

        $this->actingAs($user);

        $this->withSession([
            'idrole' => 5,
            'iduser' => $roleUser->iduser,
            'idrole_user' => 44
        ]);
    }

    public function test_pemilik_lihat_rekam_medis_valid()
    {
        $this->loginAsPemilik();

        $response = $this->get('/Pemilik/RekamMedis/detail-rekam-medis/5');

        $response->assertStatus(200);

        $response->assertSee('Relasi valid update');
        $response->assertSee('Normal');
        $response->assertSee('Sehat');
    }

    public function test_pemilik_lihat_rekam_medis_tidak_ditemukan()
    {
        $this->loginAsPemilik();

        $response = $this->get('/Pemilik/RekamMedis/detail-rekam-medis/999');

        $response->assertStatus(404);
    }

    public function test_pemilik_lihat_rekam_medis_relasi()
    {
        $this->loginAsPemilik();

        $response = $this->get('/Pemilik/RekamMedis/detail-rekam-medis/5');

        $response->assertStatus(200);
        $response->assertSee('Ica');
        $response->assertSee('Muntah api 5x sehari');
        $response->assertSee('Vitamin / suplemen');
    }


    public function test_pemilik_lihat_tanpa_login()
    {
        $response = $this->get('/Pemilik/RekamMedis/detail-rekam-medis/5');

        $response->assertRedirect('/login');
    }

    public function test_pemilik_lihat_bukan_pemilik()
    {
        $user = User::find(28); 

        $this->actingAs($user);

        $this->withSession([
            'idrole' => 2
        ]);

        $response = $this->get('/Pemilik/RekamMedis/detail-rekam-medis/5');

        $response->assertSessionHas('error');
    }
}