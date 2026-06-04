<?php

namespace App\OOP;

class Guru {
    private string $idGuru;
    private string $namaGuru;
    private string $mataPelajaran;

    public function __construct(string $idGuru, string $namaGuru, string $mataPelajaran) {
        $this->idGuru = $idGuru;
        $this->namaGuru = $namaGuru;
        $this->mataPelajaran = $mataPelajaran;
    }

    public function getIdGuru(): string {
        return $this->idGuru;
    }

    public function setIdGuru(string $idGuru): void {
        $this->idGuru = $idGuru;
    }

    public function getNamaGuru(): string {
        return $this->namaGuru;
    }

    public function setNamaGuru(string $namaGuru): void {
        $this->namaGuru = $namaGuru;
    }

    public function getMataPelajaran(): string {
        return $this->mataPelajaran;
    }

    public function setMataPelajaran(string $mataPelajaran): void {
        $this->mataPelajaran = $mataPelajaran;
    }

    /**
     * Mengubah objek menjadi array assosiatif.
     * 
     * @return array
     */
    public function toArray(): array {
        return [
            'id_guru'        => $this->idGuru,
            'nama_guru'      => $this->namaGuru,
            'mata_pelajaran' => $this->mataPelajaran
        ];
    }
}
