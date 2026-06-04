<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div style="display: flex; flex-direction: column; gap: 1rem;">
    <div class="section-header">
        <h3 class="section-title">Riwayat Banding Nilai Anda</h3>
        <a href="<?= base_url('/laporan/rapor/' . session()->get('ref_id')) ?>" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-file-invoice"></i>
            <span>Lihat Rapor & Ajukan</span>
        </a>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th>Nilai Asal</th>
                        <th>Alasan Anda</th>
                        <th>Status</th>
                        <th>Tanggapan Guru</th>
                        <th>Waktu Pengajuan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">Anda belum pernah mengajukan banding nilai.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--primary);">
                                    <?= esc($r['mata_pelajaran']) ?>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">Guru: <?= esc($r['nama_guru']) ?></div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= number_format($r['nilai_akhir'], 2) ?></div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">T:<?= number_format($r['nilai_tugas'], 1) ?> U:<?= number_format($r['nilai_uts'], 1) ?> A:<?= number_format($r['nilai_uas'], 1) ?></div>
                                </td>
                                <td style="font-size: 0.85rem; max-width: 250px; white-space: normal; word-wrap: break-word;"><?= esc($r['alasan']) ?></td>
                                <td>
                                    <?php 
                                    $badge = 'badge-warning';
                                    if ($r['status'] === 'Disetujui') $badge = 'badge-success';
                                    if ($r['status'] === 'Ditolak') $badge = 'badge-danger';
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= esc($r['status']) ?></span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted); max-width: 250px; white-space: normal; word-wrap: break-word;">
                                    <?= $r['keterangan_guru'] ? esc($r['keterangan_guru']) : '<em>Belum ditanggapi</em>' ?>
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-light);"><?= esc($r['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
