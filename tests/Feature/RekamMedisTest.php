<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;

class RekamMedisTest extends TestCase
{
    use WithoutMiddleware;

    public function test_create_rekam_medis_success()
    {
        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 17, 
            'anamnesa' => 'Demam tinggi',
            'temuan_klinis' => 'Suhu badan tinggi',
            'diagnosa' => 'Infeksi',
            'detail' => 'Diberi obat antibiotik',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertStatus(302);
    }

    public function test_update_rekam_medis_success()
    {
        $id = DB::table('rekam_medis')->value('idrekam_medis');

        if (!$id) {
            $this->markTestSkipped('Data rekam medis tidak ada');
        }

        $response = $this->put("/Admin/RekamMedis/update-rekam-medis/$id", [
            'anamnesa' => 'Demam ringan',
            'temuan_klinis' => 'Suhu normal',
            'diagnosa' => 'Sembuh',
            'detail' => 'Kontrol ulang',
            'idkode_tindakan_terapi' => 1
        ]);

        $this->assertContains($response->status(), [302, 404]);
    }

    public function test_delete_rekam_medis_success()
    {
        $id = DB::table('rekam_medis')->value('idrekam_medis');

        if (!$id) {
            $this->markTestSkipped('Data rekam medis tidak ada');
        }

        $response = $this->delete("/Admin/RekamMedis/delete-rekam-medis/$id");

        $this->assertContains($response->status(), [302, 404]);
    }

    public function test_view_rekam_medis_success()
    {
        $response = $this->get('/Admin/RekamMedis/daftar-rekam-medis');

        $response->assertStatus(200);
    }

    public function test_create_rekam_medis_failed_empty_reservasi()
    {
        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => null,
            'anamnesa' => 'Demam',
            'temuan_klinis' => 'Suhu tinggi',
            'diagnosa' => 'Infeksi',
            'detail' => 'Diberi obat',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertStatus(302);
    }

    public function test_create_rekam_medis_failed_empty_anamnesa()
    {
        $response = $this->post('/Admin/RekamMedis/store-rekam-medis', [
            'idreservasi_dokter' => 17,
            'anamnesa' => null,
            'temuan_klinis' => 'Suhu tinggi',
            'diagnosa' => 'Infeksi',
            'detail' => 'Diberi obat',
            'idkode_tindakan_terapi' => 1
        ]);

        $response->assertStatus(302);
    }
}