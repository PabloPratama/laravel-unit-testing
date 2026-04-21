<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class RegistrasiPasienTest extends TestCase
{
    // ===== POSITIF =====
    public function test_registrasi_pasien_berhasil()
    {
        DB::table('pet')->insert([
            'nama' => 'Kucing Baru',
            'tanggal_lahir' => '2025-01-01',
            'warna_tanda' => 'Putih',
            'jenis_kelamin' => 'B',
            'idpemilik' => 10,
            'idras_hewan' => 18
        ]);

        $this->assertDatabaseHas('pet', [
            'nama' => 'Kucing Baru',
            'idpemilik' => 10
        ]);
    }

    public function test_registrasi_pasien_relasi_valid()
    {
        DB::table('pet')->insert([
            'nama' => 'Anjing Test',
            'tanggal_lahir' => '2025-02-02',
            'warna_tanda' => 'Coklat',
            'jenis_kelamin' => 'J',
            'idpemilik' => 11,
            'idras_hewan' => 1
        ]);

        $this->assertDatabaseHas('pet', [
            'nama' => 'Anjing Test',
            'idras_hewan' => 1
        ]);
    }

    // ===== NEGATIF =====
    public function test_registrasi_pasien_gagal_idpemilik_tidak_ada()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('pet')->insert([
            'nama' => 'Pet Gagal',
            'tanggal_lahir' => '2025-03-03',
            'warna_tanda' => 'Hitam',
            'jenis_kelamin' => 'J',
            'idpemilik' => 999,
            'idras_hewan' => 18
        ]);
    }

    public function test_registrasi_pasien_gagal_ras_null()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('pet')->insert([
            'nama' => 'Pet Null Ras',
            'tanggal_lahir' => '2025-04-04',
            'warna_tanda' => 'Abu',
            'jenis_kelamin' => 'B',
            'idpemilik' => 10,
            'idras_hewan' => null
        ]);
    }
}