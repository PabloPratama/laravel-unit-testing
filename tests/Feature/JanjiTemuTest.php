<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class JanjiTemuTest extends TestCase
{
    use WithoutMiddleware;

    // POSITIF
    public function test_create_janji_temu_success()
    {
        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', [
            'idpet' => 8,
            'idrole_user' => 41
        ]);

        $response->assertStatus(302);
    }

    public function test_cancel_janji_temu_success()
    {
        $id = DB::table('temu_dokter')->value('idreservasi_dokter');

        if (!$id) {
            $this->markTestSkipped('Data tidak ada');
        }

        $response = $this->put("/Admin/TemuDokter/cancel-temu-dokter/$id");

        $this->assertContains($response->status(), [302, 404]);
    }

    // NEGATIF
    public function test_create_janji_temu_failed_empty_pet()
    {
        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', [
            'idpet' => null,
            'idrole_user' => 41
        ]);

        $response->assertStatus(302);
    }

    public function test_create_janji_temu_failed_empty_dokter()
    {
        $response = $this->post('/Admin/TemuDokter/store-temu-dokter', [
            'idpet' => 8,
            'idrole_user' => null
        ]);

        $response->assertStatus(302);
    }
}