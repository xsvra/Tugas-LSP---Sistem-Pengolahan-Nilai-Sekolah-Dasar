<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <?php if (session()->get('role') !== 'siswa'): ?>
        <a href="<?= base_url('/laporan') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
    <?php else: ?>
        <div></div> <!-- Spacer -->
    <?php endif; ?>
    <button onclick="window.print();" class="btn btn-primary">
        <i class="fa-solid fa-print"></i>
        <span>Cetak Rapor (Print)</span>
    </button>
</div>

<div class="report-sheet !max-w-none w-full">
    <!-- Kop Surat Sekolah -->
    <div class="report-header">
        <h3 style="text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 0.25rem;">Laporan Hasil Belajar Siswa (Rapor)</h3>
        <h2>SD NEGERI 02 BAHAGIA</h2>
        <p style="font-style: italic; font-size: 0.85rem; margin-top: 0.25rem;">Jl. Sejahtera, Ujung Harapan, Babelan, Kab. Bekasi 17612 | Telp: (021) 555-0123</p>
    </div>

    <!-- Informasi Siswa -->
    <div class="report-meta">
        <div class="meta-group">
            <span class="meta-label">Nama Siswa</span>
            <span class="meta-value"><?= esc($siswa['nama']) ?></span>
        </div>
        <div class="meta-group">
            <span class="meta-label">Nomor Induk Siswa (NIS)</span>
            <span class="meta-value"><?= esc($siswa['nis']) ?></span>
        </div>
        <div class="meta-group">
            <span class="meta-label">Kelas</span>
            <span class="meta-value"><?= esc($siswa['kelas']) ?></span>
        </div>
        <div class="meta-group">
            <span class="meta-label">Tahun Ajaran / Semester</span>
            <span class="meta-value">2026 / Ganjil</span>
        </div>
    </div>

    <!-- Tabel Nilai Detail -->
    <div style="margin-top: 2rem;">
        <h4 style="font-family: var(--font-heading); font-size: 1.1rem; margin-bottom: 0.75rem; color: var(--text-main);">A. Nilai Mata Pelajaran</h4>
        <div class="table-container" style="border: 1px solid var(--border-color); overflow-x: auto; width: 100%; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm);">
            <table class="custom-table" style="width: 100%; min-width: 700px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengampu</th>
                        <th>Tugas (30%)</th>
                        <th>UTS (30%)</th>
                        <th>UAS (40%)</th>
                        <th>Nilai Akhir</th>
                        <th>Status</th>
                        <?php if (session()->get('role') === 'siswa'): ?>
                            <th class="no-print" style="width: 120px; text-align: center;">Banding</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($nilai)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 2rem;">Siswa ini belum memiliki data nilai mata pelajaran.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($nilai as $n): ?>
                            <tr>
                                <td style="text-align: center; font-weight: 600;"><?= $no++ ?></td>
                                <td style="font-weight: 700;"><?= esc($n['mata_pelajaran']) ?></td>
                                <td><?= esc($n['nama_guru']) ?></td>
                                <td><?= number_format($n['nilai_tugas'], 1) ?></td>
                                <td><?= number_format($n['nilai_uts'], 1) ?></td>
                                <td><?= number_format($n['nilai_uas'], 1) ?></td>
                                <td style="font-weight: 700; color: var(--primary);"><?= number_format($n['nilai_akhir'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $n['status_kelulusan'] == 'Lulus' ? 'badge-success' : 'badge-danger' ?>" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;">
                                        <?= esc($n['status_kelulusan']) ?>
                                    </span>
                                </td>
                                <?php if (session()->get('role') === 'siswa'): ?>
                                    <td class="no-print" style="text-align: center;">
                                        <a href="<?= base_url('/banding/ajukan/' . $n['id']) ?>" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i class="fa-solid fa-triangle-exclamation" style="color: var(--warning);"></i>
                                            <span>Banding</span>
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ringkasan Deskripsi Kelulusan -->
    <div style="margin-top: 2rem; padding: 1.25rem; background-color: var(--bg-main); border-radius: var(--border-radius-md); border: 1px solid var(--border-color); display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
        <div>
            <h5 style="font-weight: 700; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Hasil Penilaian Akhir</h5>
            <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                <span style="font-size: 2.25rem; font-weight: 800; font-family: var(--font-heading); color: var(--primary);"><?= number_format($statistik['rata_rata'], 2) ?></span>
                <span style="font-size: 0.85rem; color: var(--text-muted);">Rata-rata Nilai</span>
            </div>
        </div>
        <div>
            <h5 style="font-weight: 700; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Keputusan Kelulusan</h5>
            <div style="margin-top: 0.5rem;">
                <?php if ($statistik['rata_rata'] >= 75): ?>
                    <span class="badge badge-success" style="font-size: 1.1rem; padding: 0.4rem 1rem;">
                        <i class="fa-solid fa-circle-check" style="margin-right: 0.25rem;"></i> LULUS
                    </span>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Siswa dinyatakan LULUS karena nilai rata-rata keseluruhan berada di atas KKM (75).</p>
                <?php else: ?>
                    <span class="badge badge-danger" style="font-size: 1.1rem; padding: 0.4rem 1rem;">
                        <i class="fa-solid fa-circle-xmark" style="margin-right: 0.25rem;"></i> TIDAK LULUS
                    </span>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Siswa dinyatakan TIDAK LULUS karena nilai rata-rata keseluruhan berada di bawah KKM (75).</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="report-signatures">
        <div class="signature-box">
            <span class="signature-title">Mengetahui,<br>Orang Tua / Wali Siswa</span>
            <div style="height: 4rem;"></div>
            <span class="signature-name"><?= !empty($siswa['nama_wali']) ? esc($siswa['nama_wali']) : '....................................................' ?></span>
        </div>
        <div class="signature-box">
            <span class="signature-title">Jakarta, <?= date('d F Y') ?><br>Wali Kelas</span>
            <div style="height: 4rem;"></div>
            <span class="signature-name">Administrator, S.Pd.</span>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
