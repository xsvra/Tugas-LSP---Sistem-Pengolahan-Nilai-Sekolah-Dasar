<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div style="display: flex; flex-direction: column; gap: 1rem;">
    <div class="section-header">
        <h3 class="section-title">Tinjau Banding Nilai Siswa</h3>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Siswa & Kelas</th>
                        <th>Mapel</th>
                        <th>Nilai Asal</th>
                        <th>Alasan Siswa</th>
                        <th>Status</th>
                        <th>Tanggapan Anda</th>
                        <th style="width: 150px; text-align: center;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appeals)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada pengajuan banding nilai dari siswa.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appeals as $a): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600;"><?= esc($a['nama_siswa']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">NIS: <?= esc($a['nis']) ?> (<?= esc($a['kelas']) ?>)</div>
                                </td>
                                <td style="font-weight: 700; color: var(--primary);"><?= esc($a['mata_pelajaran']) ?></td>
                                <td>
                                    <div style="font-weight: 600;"><?= number_format($a['nilai_akhir'], 2) ?></div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">T:<?= number_format($a['nilai_tugas'], 1) ?> U:<?= number_format($a['nilai_uts'], 1) ?> A:<?= number_format($a['nilai_uas'], 1) ?></div>
                                </td>
                                <td style="font-size: 0.85rem; max-width: 200px; white-space: normal; word-wrap: break-word;"><?= esc($a['alasan']) ?></td>
                                <td>
                                    <?php 
                                    $badge = 'badge-warning';
                                    if ($a['status'] === 'Disetujui') $badge = 'badge-success';
                                    if ($a['status'] === 'Ditolak') $badge = 'badge-danger';
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= esc($a['status']) ?></span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted); max-width: 200px; white-space: normal; word-wrap: break-word;">
                                    <?= $a['keterangan_guru'] ? esc($a['keterangan_guru']) : '<em>Belum ditanggapi</em>' ?>
                                </td>
                                <td>
                                    <div style="display: flex; justify-content: center;">
                                        <?php if ($a['status'] === 'Pending'): ?>
                                            <button onclick="toggleResponseForm(<?= $a['id'] ?>);" class="btn btn-primary btn-sm">
                                                <i class="fa-solid fa-comment-dots"></i>
                                                <span>Tanggapi</span>
                                            </button>
                                        <?php else: ?>
                                            <button onclick="toggleResponseForm(<?= $a['id'] ?>);" class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Ubah Tanggapan</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- Hidden Form Row for Response -->
                            <tr id="response-row-<?= $a['id'] ?>" style="display: none; background-color: #f8fafc;">
                                <td colspan="7" style="padding: 1.5rem 2rem; border-bottom: 2px solid var(--border-color);">
                                    <div style="max-width: 600px; margin: 0 auto;">
                                        <h4 style="font-family: var(--font-heading); font-size: 0.95rem; margin-bottom: 1rem; color: var(--primary-dark);">Tanggapan Banding Nilai: <?= esc($a['nama_siswa']) ?></h4>
                                        
                                        <form action="<?= base_url('/banding/tinjau/update/' . $a['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            
                                            <div class="form-group" style="margin-bottom: 1rem;">
                                                <label for="status-<?= $a['id'] ?>">Keputusan Status</label>
                                                <select name="status" id="status-<?= $a['id'] ?>" class="form-control" required>
                                                    <option value="Disetujui" <?= $a['status'] === 'Disetujui' ? 'selected' : '' ?>>Disetujui (Nilai akan disesuaikan oleh guru)</option>
                                                    <option value="Ditolak" <?= $a['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak (Nilai asli tetap berlaku)</option>
                                                </select>
                                            </div>

                                            <div class="form-group" style="margin-bottom: 1.25rem;">
                                                <label for="keterangan-<?= $a['id'] ?>">Keterangan / Umpan Balik Guru</label>
                                                <textarea name="keterangan_guru" id="keterangan-<?= $a['id'] ?>" class="form-control" rows="3" placeholder="Tuliskan catatan persetujuan atau alasan penolakan banding..." required><?= esc($a['keterangan_guru']) ?></textarea>
                                            </div>

                                            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                                <button type="button" onclick="toggleResponseForm(<?= $a['id'] ?>);" class="btn btn-secondary btn-sm">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa-solid fa-floppy-disk"></i>
                                                    <span>Kirim Keputusan</span>
                                                </button>
                                            </div>
                                        </form>
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

<script>
    function toggleResponseForm(id) {
        const row = document.getElementById('response-row-' + id);
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    }
</script>

<?= $this->endSection() ?>
