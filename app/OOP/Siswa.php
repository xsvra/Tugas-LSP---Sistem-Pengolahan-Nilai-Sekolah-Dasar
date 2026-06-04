<?php

namespace App\OOP;

class Siswa {
    private string $nis;
    private string $nama;
    private string $kelas;

    public function __construct(string $nis, string $nama, string $kelas) {
        $this->nis = $nis;
        $this->nama = $nama;
        $this->kelas = $kelas;
    }

    public function getNis(): string {
        return $this->nis;
    }

    public function setNis(string $nis): void {
        $this->nis = $nis;
    }

    public function getNama(): string {
        return $this->nama;
    }

    public function setNama(string $nama): void {
        $this->nama = $nama;
    }

    public function getKelas(): string {
        return $this->kelas;
    }

    public function setKelas(string $kelas): void {
        $this->kelas = $kelas;
    }

    /**
     * Mengubah objek menjadi array assosiatif.
     * 
     * @return array
     */
    public function toArray(): array {
        return [
            'nis'   => $this->nis,
            'nama'  => $this->nama,
            'kelas' => $this->kelas
        ];
    }
}
