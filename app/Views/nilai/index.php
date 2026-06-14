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

    <?php if (session()->get('role') === 'guru' && (!empty($kelasDiajar) || !empty($mapelDiajar))): ?>
        <div style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap; background: #ffffff; padding: 1rem; border-radius: var(--border-radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
            <!-- Filter Mata Pelajaran -->
            <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                <span style="font-weight: 600; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Filter Mapel</span>
                <select id="filter-mapel" class="form-control" style="width: auto; min-width: 180px; padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    <option value="all">Semua Mapel</option>
                    <?php foreach ($mapelDiajar as $m): ?>
                        <option value="<?= esc($m) ?>"><?= esc($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Filter Kelas -->
            <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                <span style="font-weight: 600; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Filter Kelas</span>
                <select id="filter-kelas" class="form-control" style="width: auto; min-width: 150px; padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    <option value="all">Semua Kelas</option>
                    <?php foreach ($kelasDiajar as $k): ?>
                        <option value="<?= esc($k) ?>">Kelas <?= esc($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Mata Pelajaran</th>
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
                            <tr data-class="<?= esc($n['kelas']) ?>" data-mapel="<?= esc($n['mata_pelajaran']) ?>">
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

<script>
// Combined filter: by class AND by mapel
function applyFilters() {
    const mapelVal = document.getElementById('filter-mapel')?.value || 'all';
    const kelasVal = document.getElementById('filter-kelas')?.value || 'all';

    document.querySelectorAll('tbody tr').forEach(row => {
        const rowClass = row.getAttribute('data-class');
        const rowMapel = row.getAttribute('data-mapel');
        if (!rowClass && !rowMapel) return; // skip empty-message rows

        const matchClass = (kelasVal === 'all' || rowClass === kelasVal);
        const matchMapel = (mapelVal === 'all' || rowMapel === mapelVal);

        row.style.display = (matchClass && matchMapel) ? '' : 'none';
    });
}

document.getElementById('filter-mapel')?.addEventListener('change', applyFilters);
document.getElementById('filter-kelas')?.addEventListener('change', applyFilters);
</script>

<?= $this->endSection() ?>
