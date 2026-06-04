<?php

namespace App\OOP;

class Nilai {
    private ?int $id;
    private Siswa $siswa;
    private Guru $guru;
    private float $nilaiTugas;
    private float $nilaiUts;
    private float $nilaiUas;
    private float $nilaiAkhir;
    private string $statusKelulusan;

    private function loadHelper(): void {
        if (!function_exists('validasi_nilai')) {
            if (function_exists('helper')) {
                helper('structured');
            } else {
                require_once dirname(__DIR__) . '/Helpers/structured_helper.php';
            }
        }
    }

    public function __construct(
        ?int $id,
        Siswa $siswa,
        Guru $guru,
        float $nilaiTugas,
        float $nilaiUts,
        float $nilaiUas
    ) {
        // Load helper structured_helper.php
        $this->loadHelper();

        if (!validasi_nilai($nilaiTugas) || !validasi_nilai($nilaiUts) || !validasi_nilai($nilaiUas)) {
            throw new \InvalidArgumentException("Nilai harus berada dalam rentang 0-100.");
        }

        $this->id = $id;
        $this->siswa = $siswa;
        $this->guru = $guru;
        $this->nilaiTugas = $nilaiTugas;
        $this->nilaiUts = $nilaiUts;
        $this->nilaiUas = $nilaiUas;

        $this->kalkulasi();
    }

    /**
     * Melakukan kalkulasi nilai akhir dan kelulusan menggunakan helper.
     */
    private function kalkulasi(): void {
        $this->loadHelper();
        $this->nilaiAkhir = hitung_nilai_akhir($this->nilaiTugas, $this->nilaiUts, $this->nilaiUas);
        $this->statusKelulusan = tentukan_status_kelulusan($this->nilaiAkhir);
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getSiswa(): Siswa {
        return $this->siswa;
    }

    public function setSiswa(Siswa $siswa): void {
        $this->siswa = $siswa;
    }

    public function getGuru(): Guru {
        return $this->guru;
    }

    public function setGuru(Guru $guru): void {
        $this->guru = $guru;
    }

    public function getNilaiTugas(): float {
        return $this->nilaiTugas;
    }

    public function setNilaiTugas(float $nilaiTugas): void {
        $this->loadHelper();
        if (!validasi_nilai($nilaiTugas)) {
            throw new \InvalidArgumentException("Nilai Tugas tidak valid.");
        }
        $this->nilaiTugas = $nilaiTugas;
        $this->kalkulasi();
    }

    public function getNilaiUts(): float {
        return $this->nilaiUts;
    }

    public function setNilaiUts(float $nilaiUts): void {
        $this->loadHelper();
        if (!validasi_nilai($nilaiUts)) {
            throw new \InvalidArgumentException("Nilai UTS tidak valid.");
        }
        $this->nilaiUts = $nilaiUts;
        $this->kalkulasi();
    }

    public function getNilaiUas(): float {
        return $this->nilaiUas;
    }

    public function setNilaiUas(float $nilaiUas): void {
        $this->loadHelper();
        if (!validasi_nilai($nilaiUas)) {
            throw new \InvalidArgumentException("Nilai UAS tidak valid.");
        }
        $this->nilaiUas = $nilaiUas;
        $this->kalkulasi();
    }

    public function getNilaiAkhir(): float {
        return $this->nilaiAkhir;
    }

    public function getStatusKelulusan(): string {
        return $this->statusKelulusan;
    }

    /**
     * Mengubah objek menjadi array assosiatif untuk database.
     * 
     * @return array
     */
    public function toArray(): array {
        return [
            'id'               => $this->id,
            'nis'              => $this->siswa->getNis(),
            'id_guru'          => $this->guru->getIdGuru(),
            'nilai_tugas'      => $this->nilaiTugas,
            'nilai_uts'        => $this->nilaiUts,
            'nilai_uas'        => $this->nilaiUas,
            'nilai_akhir'      => $this->nilaiAkhir,
            'status_kelulusan' => $this->statusKelulusan
        ];
    }
}
