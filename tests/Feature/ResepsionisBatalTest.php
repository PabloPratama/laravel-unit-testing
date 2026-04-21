<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ResepsionisBatalTest extends TestCase
{
    protected function loginAsResepsionis()
    {
        $user = DB::table('user')
            ->join('role_user', 'user.iduser', '=', 'role_user.iduser')
            ->where('role_user.idrole', 4)
            ->where('role_user.status', 1)
            ->select('user.*')
            ->first();

        $this->actingAs(User::find($user->iduser));

        $this->withSession([
            'idrole' => 4,
            'iduser' => $user->iduser
        ]);
    }

    public function test_batal_reservasi_berhasil()
    {
        $this->loginAsResepsionis();

        $reservasi = DB::table('temu_dokter')
            ->whereNull('deleted_at')
            ->where('idreservasi_dokter', 29)
            ->first();

        $this->assertNotNull($reservasi);

        $response = $this->put('/Resepsionis/TemuDokter/cancel-temu-dokter/' . $reservasi->idreservasi_dokter);

        $response->assertRedirect('/Resepsionis/TemuDokter/daftar-temu-dokter');

        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $reservasi->idreservasi_dokter,
        ]);

        $this->assertNotNull(
            DB::table('temu_dokter')
                ->where('idreservasi_dokter', $reservasi->idreservasi_dokter)
                ->value('deleted_at')
        );
    }

    public function test_batal_dengan_id_tidak_ada()
    {
        $this->loginAsResepsionis();

        $response = $this->put('/Resepsionis/TemuDokter/cancel-temu-dokter/9999');

        $response->assertRedirect('/Resepsionis/TemuDokter/daftar-temu-dokter');

        $this->assertDatabaseMissing('temu_dokter', [
            'idreservasi_dokter' => 9999
        ]);
    }

    public function test_batal_tidak_merusak_relasi_pet_dan_roleuser()
    {
        $this->loginAsResepsionis();

        $reservasi = DB::table('temu_dokter')
            ->whereNull('deleted_at')
            ->where('idreservasi_dokter', 24)
            ->first();

        $this->assertNotNull($reservasi);

        $this->put('/Resepsionis/TemuDokter/cancel-temu-dokter/' . $reservasi->idreservasi_dokter);

        $this->assertDatabaseHas('pet', [
            'idpet' => $reservasi->idpet
        ]);

        $this->assertDatabaseHas('role_user', [
            'idrole_user' => $reservasi->idrole_user
        ]);
    }

    public function test_batal_reservasi_yang_sudah_dibatalkan()
    {
        $this->loginAsResepsionis();

        $reservasi = DB::table('temu_dokter')
            ->whereNotNull('deleted_at')
            ->where('idreservasi_dokter', 28)
            ->first();

        $this->assertNotNull($reservasi);

        $response = $this->put('/Resepsionis/TemuDokter/cancel-temu-dokter/' . $reservasi->idreservasi_dokter);

        $response->assertRedirect('/Resepsionis/TemuDokter/daftar-temu-dokter');

        $this->assertDatabaseHas('temu_dokter', [
            'idreservasi_dokter' => $reservasi->idreservasi_dokter
        ]);
    }
}