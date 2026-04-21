<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminBatalTDTest extends TestCase
{
    protected function loginAsAdmin()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->where('role_user.idrole', 1)
            ->where('role_user.status', 1)
            ->select('user.*')
            ->first();

        $this->actingAs(User::find($user->iduser));

        $this->withSession([
            'idrole' => 1,
            'iduser' => $user->iduser
        ]);
    }

    public function test_batal_reservasi_berhasil()
    {
        $this->loginAsAdmin();

        $reservasi = DB::table('temu_dokter')
            ->whereNull('deleted_at')
            ->where('idreservasi_dokter', 18)
            ->first();

        $this->assertNotNull($reservasi);

        $response = $this->put('/Admin/TemuDokter/cancel-temu-dokter/' . $reservasi->idreservasi_dokter);

        $response->assertRedirect('/Admin/TemuDokter/daftar-temu-dokter');

        $this->assertNotNull(
            DB::table('temu_dokter')
                ->where('idreservasi_dokter', 18)
                ->value('deleted_at')
        );
    }

    public function test_batal_dengan_id_tidak_ada()
    {
        $this->loginAsAdmin();

        $response = $this->put('/Admin/TemuDokter/cancel-temu-dokter/66666'); 

        $response->assertStatus(404); 

        $this->assertDatabaseMissing('temu_dokter', [
            'idreservasi_dokter' => 66666
        ]);
    }

    public function test_batal_tidak_merusak_relasi_pet_dan_roleuser()
    {
        $this->loginAsAdmin();

        $reservasi = DB::table('temu_dokter')
            ->whereNull('deleted_at')
            ->where('idreservasi_dokter', 19) 
            ->first();

        $this->assertNotNull($reservasi);

        $this->put('/Admin/TemuDokter/cancel-temu-dokter/' . $reservasi->idreservasi_dokter);

        $this->assertDatabaseHas('pet', [
            'idpet' => $reservasi->idpet
        ]);

        $this->assertDatabaseHas('role_user', [
            'idrole_user' => $reservasi->idrole_user
        ]);
    }

    public function test_batal_reservasi_yang_sudah_dibatalkan()
    {
        $this->loginAsAdmin();

        $reservasi = DB::table('temu_dokter')
            ->whereNotNull('deleted_at')
            ->where('idreservasi_dokter', 17)
            ->first();

        $this->assertNotNull($reservasi);

        $response = $this->put('/Admin/TemuDokter/cancel-temu-dokter/' . $reservasi->idreservasi_dokter);

        $response->assertRedirect('/Admin/TemuDokter/daftar-temu-dokter');

        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => 17
        ]);
    }
}