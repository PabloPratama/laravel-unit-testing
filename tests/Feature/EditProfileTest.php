<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class EditProfileTest extends TestCase
{
    private function uniqueEmail($prefix)
    {
        return $prefix . '_' . time() . rand(100,999) . '@mail.com';
    }

    // ===== ADMIN =====
    public function test_edit_profil_admin_berhasil()
    {
        $email = $this->uniqueEmail('admin');

        $id = DB::table('user')->insertGetId([
            'nama' => 'Admin Lama',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        $newEmail = $this->uniqueEmail('adminbaru');

        DB::table('user')->where('iduser', $id)->update([
            'nama' => 'Admin Baru',
            'email' => $newEmail
        ]);

        $this->assertDatabaseHas('user', [
            'iduser' => $id,
            'nama' => 'Admin Baru',
            'email' => $newEmail
        ]);
    }

    public function test_edit_profil_admin_email_duplikat()
    {
        $email = $this->uniqueEmail('duplicate');

        DB::table('user')->insert([
            'nama' => 'User1',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('user')->insert([
            'nama' => 'User2',
            'email' => $email,
            'password' => bcrypt('123')
        ]);
    }

    // ===== RESEPSIONIS =====
    public function test_edit_profil_resepsionis_berhasil()
    {
        $email = $this->uniqueEmail('resepsionis');

        $id = DB::table('user')->insertGetId([
            'nama' => 'Resepsionis Lama',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        DB::table('user')->where('iduser', $id)->update([
            'nama' => 'Resepsionis Baru'
        ]);

        $this->assertDatabaseHas('user', [
            'iduser' => $id,
            'nama' => 'Resepsionis Baru'
        ]);
    }

    public function test_edit_profil_resepsionis_email_duplikat()
    {
        $email = $this->uniqueEmail('same');

        DB::table('user')->insert([
            'nama' => 'User1',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('user')->insert([
            'nama' => 'User2',
            'email' => $email,
            'password' => bcrypt('123')
        ]);
    }

    // ===== DOKTER =====
    public function test_edit_profil_dokter_berhasil()
    {
        $email = $this->uniqueEmail('dokter');

        $id = DB::table('user')->insertGetId([
            'nama' => 'Dokter Lama',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        DB::table('dokter')->insert([
            'iduser' => $id,
            'no_hp' => '0811111111',
            'alamat' => 'Alamat Lama',
            'bidang_dokter' => 'Umum',
            'jenis_kelamin' => 'L'
        ]);

        DB::table('user')->where('iduser', $id)->update([
            'nama' => 'Dokter Baru'
        ]);

        DB::table('dokter')->where('iduser', $id)->update([
            'alamat' => 'Alamat Baru'
        ]);

        $this->assertDatabaseHas('user', [
            'iduser' => $id,
            'nama' => 'Dokter Baru'
        ]);

        $this->assertDatabaseHas('dokter', [
            'iduser' => $id,
            'alamat' => 'Alamat Baru'
        ]);
    }

    public function test_edit_profil_dokter_nohp_kosong()
    {
        $email = $this->uniqueEmail('dokterfail');

        $id = DB::table('user')->insertGetId([
            'nama' => 'Dokter Fail',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('dokter')->insert([
            'iduser' => $id,
            'no_hp' => null, 
            'alamat' => 'Test',
            'bidang_dokter' => 'Umum',
            'jenis_kelamin' => 'L'
        ]);
    }

    // ===== PERAWAT =====
    public function test_edit_profil_perawat_berhasil()
    {
        $email = $this->uniqueEmail('perawat');

        $id = DB::table('user')->insertGetId([
            'nama' => 'Perawat Lama',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        DB::table('perawat')->insert([
            'iduser' => $id,
            'no_hp' => '0812222222',
            'alamat' => 'Lama',
            'pendidikan' => 'D3',
            'jenis_kelamin' => 'P'
        ]);

        DB::table('perawat')->where('iduser', $id)->update([
            'pendidikan' => 'S1'
        ]);

        $this->assertDatabaseHas('perawat', [
            'iduser' => $id,
            'pendidikan' => 'S1'
        ]);
    }

    public function test_edit_profil_perawat_pendidikan_kosong()
    {
        $email = $this->uniqueEmail('perawatfail');

        $id = DB::table('user')->insertGetId([
            'nama' => 'Perawat Fail',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('perawat')->insert([
            'iduser' => $id,
            'no_hp' => '0812222222',
            'alamat' => 'Test',
            'pendidikan' => null, 
            'jenis_kelamin' => 'P'
        ]);
    }

    // ===== PEMILIK =====
    public function test_edit_profil_pemilik_berhasil()
    {
        $email = $this->uniqueEmail('pemilik');

        $id = DB::table('user')->insertGetId([
            'nama' => 'Pemilik Lama',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        DB::table('pemilik')->insert([
            'iduser' => $id,
            'no_wa' => '0813333333',
            'alamat' => 'Alamat Lama'
        ]);

        DB::table('pemilik')->where('iduser', $id)->update([
            'alamat' => 'Alamat Baru'
        ]);

        $this->assertDatabaseHas('pemilik', [
            'iduser' => $id,
            'alamat' => 'Alamat Baru'
        ]);
    }

    public function test_edit_profil_pemilik_alamat_kosong()
    {
        $email = $this->uniqueEmail('pemilikfail');

        $id = DB::table('user')->insertGetId([
            'nama' => 'Pemilik Fail',
            'email' => $email,
            'password' => bcrypt('123')
        ]);

        DB::table('pemilik')->insert([
            'iduser' => $id,
            'no_wa' => '0813333333',
            'alamat' => null
        ]);

        $this->assertDatabaseHas('pemilik', [
            'iduser' => $id,
            'alamat' => null
        ]);
    }
}