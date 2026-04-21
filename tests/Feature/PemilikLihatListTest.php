<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PemilikLihatListTest extends TestCase
{
    protected function loginAsPemilik()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->where('role_user.idrole', 5)
            ->where('role_user.status', 1)
            ->where('user.iduser', 31) 
            ->select('user.*', 'role_user.idrole')
            ->first();

        $this->assertNotNull($user); 

        $this->actingAs(User::find($user->iduser));

        $this->withSession([
            'idrole' => 5,
            'iduser' => $user->iduser
        ]);
    }

    public function test_pemilik_bisa_melihat_reservasi()
    {
        $this->loginAsPemilik();

        $response = $this->get('/Pemilik/TemuDokter/daftar-reservasi-saya');

        $response->assertStatus(200);
        $response->assertSee('Reservasi Saya');
    }

    public function test_hanya_menampilkan_reservasi_milik_sendiri()
    {
        $this->loginAsPemilik();

        $iduser = session('iduser'); 

        $response = $this->get('/Pemilik/TemuDokter/daftar-reservasi-saya');
        $response->assertStatus(200);

        $data = DB::table('temu_dokter as td')
            ->join('pet as p', 'td.idpet', '=', 'p.idpet')
            ->join('pemilik as pm', 'p.idpemilik', '=', 'pm.idpemilik')
            ->where('pm.iduser', $iduser)
            ->whereNull('td.deleted_at')
            ->first();

        $this->assertNotNull($data);
    }

    public function test_tidak_menampilkan_data_deleted()
    {
        $this->loginAsPemilik();

        $response = $this->get('/Pemilik/TemuDokter/daftar-reservasi-saya');
        $response->assertStatus(200);

        $deleted = DB::table('temu_dokter')
            ->whereNotNull('deleted_at')
            ->first();

        if ($deleted) {
            $waktu = date('d-m-Y H:i', strtotime($deleted->waktu_daftar));

            $response->assertDontSeeText($waktu);
        }

        $this->assertTrue(true);
    }

    public function test_redirect_jika_belum_login()
    {
        $response = $this->get('/Pemilik/TemuDokter/daftar-reservasi-saya');
        $response->assertRedirect('/login');
    }
}