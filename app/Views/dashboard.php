<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- KPI Metrics Grid -->
<div class="card-grid">
    <!-- Card Siswa -->
    <div class="card stat-card primary">
        <div class="stat-header">
            <span class="stat-title">Total Siswa</span>
            <div class="stat-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
        </div>
        <div class="stat-value"><?= $total_siswa ?></div>
        <div class="stat-desc">Siswa terdaftar aktif</div>
    </div>

    <!-- Card Guru -->
    <div class="card stat-card primary">
        <div class="stat-header">
            <span class="stat-title">Total Guru</span>
            <div class="stat-icon">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
        </div>
        <div class="stat-value"><?= $total_guru ?></div>
        <div class="stat-desc">Guru mata pelajaran</div>
    </div>

    <!-- Card Rata-rata Kelas -->
    <div class="card stat-card warning">
        <div class="stat-header">
            <span class="stat-title">Rata-rata Nilai</span>
            <div class="stat-icon">
                <i class="fa-solid fa-calculator"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($statistik['rata_rata'], 2) ?></div>
        <div class="stat-desc">Rata-rata nilai akhir sekolah</div>
    </div>

    <!-- Card Kelulusan -->
    <div class="card stat-card success">
        <div class="stat-header">
            <span class="stat-title">Persentase Kelulusan</span>
            <div class="stat-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="stat-value"><?= number_format($persentase_kelulusan_siswa, 1) ?>%</div>
        <div class="stat-desc"><?= $total_lulus_siswa ?> dari <?= $total_siswa ?> siswa lulus</div>
    </div>
</div>

<!-- Secondary Statistics and Recent Grades Section -->
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-top: 1rem;">
    <!-- Small Summary Stats Card -->
    <div class="card" style="display: flex; flex-direction: column; gap: 1.5rem;">
        <h3 class="section-title">Detail Statistik Nilai</h3>
        
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                <span style="font-size: 0.9rem; color: var(--text-muted);">Nilai Tertinggi</span>
                <span class="badge badge-success" style="font-size: 1rem;"><?= number_format($statistik['tertinggi'], 2) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                <span style="font-size: 0.9rem; color: var(--text-muted);">Nilai Terendah</span>
                <span class="badge badge-danger" style="font-size: 1rem;"><?= number_format($statistik['terendah'], 2) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                <span style="font-size: 0.9rem; color: var(--text-muted);">Lulus KKM (>= 75)</span>
                <span class="badge badge-primary" style="font-size: 1rem;"><?= $total_lulus_siswa ?> Siswa</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.9rem; color: var(--text-muted);">Tidak Lulus (< 75)</span>
                <span class="badge badge-danger" style="font-size: 1rem;"><?= $total_tidak_lulus_siswa ?> Siswa</span>
            </div>
        </div>

        <div style="margin-top: auto; padding: 1rem; background-color: var(--primary-light); border-radius: var(--border-radius-md); border: 1px solid rgba(99, 102, 241, 0.1);">
            <p style="font-size: 0.85rem; color: var(--primary-dark); font-weight: 500; text-align: center; line-height: 1.4;">
                <i class="fa-solid fa-circle-info" style="margin-right: 0.25rem;"></i>
                KKM ditetapkan sebesar <strong>75</strong>. Bobot nilai akhir: Tugas (30%), UTS (30%), UAS (40%).
            </p>
        </div>
    </div>

    <!-- Recent Grades Table -->
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <h3 class="section-title" style="margin: 0;">Nilai Terbaru Diinput</h3>
            <div style="position: relative; display: flex; align-items: center;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; color: var(--text-muted); font-size: 0.9rem;"></i>
                <input type="text" id="search-nis" placeholder="Filter berdasarkan NIS..." style="padding: 0.5rem 1rem 0.5rem 2.25rem; border: 1px solid var(--border-color); border-radius: var(--border-radius-md); font-size: 0.875rem; width: 220px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border-color)'">
            </div>
        </div>

        <div class="table-container">
            <div class="table-wrapper" style="max-height: 290px; overflow-y: auto;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Mata Pelajaran</th>
                            <th>Tugas</th>
                            <th>UTS</th>
                            <th>UAS</th>
                            <th>Nilai Akhir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_nilai)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data nilai. Silakan tambahkan nilai terlebih dahulu.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_nilai as $n): ?>
                                <tr class="grade-row" data-nis="<?= esc($n['nis']) ?>">
                                    <td>
                                        <div style="font-weight: 600;"><?= esc($n['nama_siswa']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">NIS: <?= esc($n['nis']) ?> (<?= esc($n['kelas']) ?>)</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500;"><?= esc($n['mata_pelajaran']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= esc($n['nama_guru']) ?></div>
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
                                </tr>
                            <?php endforeach; ?>
                            <tr id="empty-row" style="display: none;">
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Tidak ada data nilai untuk NIS tersebut.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Performa Nilai Per Mata Pelajaran -->
<div class="card" style="margin-top: 1.5rem;">
    <h3 class="section-title" style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fa-solid fa-chart-simple" style="color: var(--primary);"></i>
        <span>Performa & Statistik Nilai Per Mata Pelajaran</span>
    </h3>
    <div class="table-container">
        <div class="table-wrapper" style="max-height: 280px; overflow-y: auto;">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th style="text-align: center;">Rata-rata Nilai</th>
                        <th style="text-align: center;">Jumlah Lulus</th>
                        <th style="text-align: center;">Jumlah Tidak Lulus</th>
                        <th>Performa Kelulusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subject_stats)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data statistik mata pelajaran.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subject_stats as $sub): ?>
                            <?php 
                            $ratio = $sub['count'] > 0 ? ($sub['lulus'] / $sub['count']) * 100 : 0;
                            
                            if ($ratio >= 90) {
                                $perfCategory = "Sangat Baik";
                                $badgeClass = "badge-success";
                                $iconClass = "fa-solid fa-circle-check";
                            } elseif ($ratio >= 75) {
                                $perfCategory = "Baik";
                                $badgeClass = "badge-primary";
                                $iconClass = "fa-solid fa-circle-check";
                            } elseif ($ratio >= 60) {
                                $perfCategory = "Cukup";
                                $badgeClass = "badge-warning";
                                $iconClass = "fa-solid fa-circle-info";
                            } else {
                                $perfCategory = "Perlu Evaluasi";
                                $badgeClass = "badge-danger";
                                $iconClass = "fa-solid fa-triangle-exclamation";
                            }
                            ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main);"><?= esc($sub['nama']) ?></td>
                                <td style="text-align: center; font-weight: 700; color: var(--primary);"><?= number_format($sub['rata_rata'], 2) ?></td>
                                <td style="text-align: center;"><span class="badge badge-success"><?= $sub['lulus'] ?> Siswa</span></td>
                                <td style="text-align: center;"><span class="badge badge-danger"><?= $sub['tidak_lulus'] ?> Siswa</span></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>" style="display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <i class="<?= $iconClass ?>"></i> <?= $perfCategory ?> (<?= number_format($ratio, 1) ?>% Lulus)
                                    </span>
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-nis');
    const rows = document.querySelectorAll('.grade-row');
    const emptyRow = document.getElementById('empty-row');

    function filterRows() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let visibleCount = 0;

        rows.forEach((row) => {
            const nis = row.getAttribute('data-nis').toLowerCase();
            const matches = nis.includes(query);

            if (query === '') {
                // Show all rows, letting CSS scrollbar handle viewability
                row.style.display = '';
                visibleCount++;
            } else {
                // If query is active, show all matching rows
                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        });

        if (visibleCount === 0) {
            if (emptyRow) emptyRow.style.display = '';
        } else {
            if (emptyRow) emptyRow.style.display = 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterRows);
    }
    
    // Run initially to show only 5 rows
    filterRows();
});
</script>

<?= $this->endSection() ?>
