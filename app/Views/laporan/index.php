<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- KPI Metrics Grid from general stats -->
<div class="card-grid">
    <div class="card stat-card primary">
        <div class="stat-header">
            <span class="stat-title">Nilai Rata-rata</span>
            <div class="stat-icon"><i class="fa-solid fa-calculator"></i></div>
        </div>
        <div class="stat-value"><?= number_format($statistik['rata_rata'], 2) ?></div>
        <div class="stat-desc">Nilai akhir rata-rata kelas</div>
    </div>
    
    <div class="card stat-card success">
        <div class="stat-header">
            <span class="stat-title">Lulus KKM</span>
            <div class="stat-icon"><i class="fa-solid fa-face-smile"></i></div>
        </div>
        <div class="stat-value"><?= $statistik['total_lulus'] ?></div>
        <div class="stat-desc">Siswa dengan nilai akhir >= 75</div>
    </div>

    <div class="card stat-card danger">
        <div class="stat-header">
            <span class="stat-title">Tidak Lulus KKM</span>
            <div class="stat-icon"><i class="fa-solid fa-face-frown"></i></div>
        </div>
        <div class="stat-value"><?= $statistik['total_tidak_lulus'] ?></div>
        <div class="stat-desc">Siswa dengan nilai akhir < 75</div>
    </div>

    <div class="card stat-card success">
        <div class="stat-header">
            <span class="stat-title">Tingkat Kelulusan</span>
            <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
        </div>
        <div class="stat-value"><?= number_format($statistik['persentase_kelulusan'], 1) ?>%</div>
        <div class="stat-desc">Rasio kelulusan sekolah</div>
    </div>
</div>

<!-- Student Grade Summary List -->
<div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem;">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <h3 class="section-title" style="margin: 0;">Rekapitulasi Nilai per Siswa</h3>
        <?php if (session()->get('role') === 'guru'): ?>
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <!-- Filter NIS -->
                <div style="position: relative; display: flex; align-items: center;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; color: var(--text-muted); font-size: 0.9rem;"></i>
                    <input type="text" id="filter-nis" placeholder="Cari NIS..." style="padding: 0.5rem 1rem 0.5rem 2.25rem; border: 1px solid var(--border-color); border-radius: var(--border-radius-md); font-size: 0.875rem; width: 180px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
                </div>
                <!-- Filter Kelulusan -->
                <div style="display: flex; align-items: center;">
                    <select id="filter-status" style="padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: var(--border-radius-md); font-size: 0.875rem; outline: none; transition: border-color 0.2s; background: white;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
                        <option value="">-- Semua Status --</option>
                        <option value="Lulus">Lulus</option>
                        <option value="Tidak Lulus">Tidak Lulus</option>
                        <option value="Belum Ada Nilai">Belum Ada Nilai</option>
                    </select>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <?php if (session()->get('role') !== 'guru'): ?>
                            <th style="text-align: center;">Jumlah Mapel</th>
                        <?php endif; ?>
                        <th>Rata-rata Nilai</th>
                        <th>Status Kelulusan</th>
                        <?php if (session()->get('role') !== 'guru'): ?>
                            <th style="width: 150px; text-align: center;">Rapor</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rekap_siswa)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data siswa untuk dilaporkan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rekap_siswa as $r): ?>
                            <?php 
                            $status_val = $r['jumlah_mapel'] > 0 ? $r['status'] : 'Belum Ada Nilai';
                            ?>
                            <tr class="rekap-row" data-nis="<?= esc($r['nis']) ?>" data-status="<?= esc($status_val) ?>">
                                <td style="font-weight: 700; color: var(--primary);"><?= esc($r['nis']) ?></td>
                                <td style="font-weight: 600;"><?= esc($r['nama']) ?></td>
                                <td><span class="badge badge-primary"><?= esc($r['kelas']) ?></span></td>
                                <?php if (session()->get('role') !== 'guru'): ?>
                                    <td style="text-align: center; font-weight: 600;"><?= $r['jumlah_mapel'] ?></td>
                                <?php endif; ?>
                                <td style="font-weight: 700; color: var(--primary);"><?= number_format($r['rata_rata'], 2) ?></td>
                                <td>
                                    <?php if ($r['jumlah_mapel'] > 0): ?>
                                        <span class="badge <?= $r['status'] == 'Lulus' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= esc($r['status']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Belum Ada Nilai</span>
                                    <?php endif; ?>
                                </td>
                                <?php if (session()->get('role') !== 'guru'): ?>
                                    <td>
                                        <div style="display: flex; justify-content: center;">
                                            <?php if ($r['jumlah_mapel'] > 0): ?>
                                                <a href="<?= base_url('/laporan/rapor/' . $r['nis']) ?>" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                                    <i class="fa-solid fa-print"></i>
                                                    <span>Cetak Rapor</span>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled style="opacity: 0.5; cursor: not-allowed;">
                                                    <i class="fa-solid fa-ban"></i>
                                                    <span>Cetak Rapor</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                            <tr id="rekap-empty-row" style="display: none;">
                                <td colspan="<?= session()->get('role') === 'guru' ? '5' : '7' ?>" style="text-align: center; color: var(--text-muted); padding: 2rem;">Tidak ada data rekapitulasi yang cocok.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterNis = document.getElementById('filter-nis');
    const filterStatus = document.getElementById('filter-status');
    const rows = document.querySelectorAll('.rekap-row');
    const emptyRow = document.getElementById('rekap-empty-row');

    function applyFilters() {
        if (!filterNis && !filterStatus) return;

        const nisQuery = filterNis ? filterNis.value.trim().toLowerCase() : '';
        const statusQuery = filterStatus ? filterStatus.value.toLowerCase() : '';

        let visibleCount = 0;

        rows.forEach(row => {
            const nis = row.getAttribute('data-nis').toLowerCase();
            const status = row.getAttribute('data-status').toLowerCase();

            const matchesNis = nis.includes(nisQuery);
            const matchesStatus = statusQuery === '' || status === statusQuery;

            if (matchesNis && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            if (emptyRow) emptyRow.style.display = '';
        } else {
            if (emptyRow) emptyRow.style.display = 'none';
        }
    }

    if (filterNis) filterNis.addEventListener('input', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);
});
</script>

<?= $this->endSection() ?>
