<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div style="display: flex; flex-direction: column; gap: 1rem;">
    <div class="section-header">
        <h3 class="section-title">Daftar Nilai Siswa</h3>
        <a href="<?= base_url('/nilai/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-file-signature"></i>
            <span>Input Nilai Baru</span>
        </a>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Mata Pelajaran (Guru)</th>
                        <th>Tugas (30%)</th>
                        <th>UTS (30%)</th>
                        <th>UAS (40%)</th>
                        <th>Nilai Akhir</th>
                        <th>Status Kelulusan</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($nilai)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data nilai. Silakan klik Input Nilai Baru.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($nilai as $n): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($n['nama_siswa']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">NIS: <?= esc($n['nis']) ?> (<?= esc($n['kelas']) ?>)</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($n['mata_pelajaran']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">Guru: <?= esc($n['nama_guru']) ?></div>
                                </td>
                                <td><?= number_format($n['nilai_tugas'], 1) ?></td>
                                <td><?= number_format($n['nilai_uts'], 1) ?></td>
                                <td><?= number_format($n['nilai_uas'], 1) ?></td>
                                <td style="font-weight: 700; color: var(--primary);"><?= number_format($n['nilai_akhir'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $n['status_kelulusan'] == 'Lulus' ? 'badge-success' : 'badge-danger' ?>">
                                        <?= esc($n['status_kelulusan']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" style="justify-content: center;">
                                        <a href="<?= base_url('/nilai/edit/' . $n['id']) ?>" class="btn btn-secondary btn-sm" title="Edit">
                                            <i class="fa-solid fa-pen-to-square" style="color: var(--secondary);"></i>
                                        </a>
                                        <a href="<?= base_url('/nilai/delete/' . $n['id']) ?>" class="btn btn-secondary btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data nilai ini?');">
                                            <i class="fa-solid fa-trash-can" style="color: var(--danger);"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
