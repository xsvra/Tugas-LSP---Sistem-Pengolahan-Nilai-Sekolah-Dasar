<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container" style="max-width: 100%; width: 100%;">
    <div class="form-card" style="width: 100%;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
            <a href="<?= base_url('/nilai') ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="section-title" style="margin: 0;">Input Nilai Siswa Baru</h3>
        </div>

        <!-- Step 1: Filter Mata Pelajaran & Kelas -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="filter_mapel">Pilih Mata Pelajaran</label>
                <select id="filter_mapel" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                    <?php foreach ($mapelDiajar as $m): ?>
                        <option value="<?= esc($m) ?>"><?= esc($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label for="filter_kelas">Pilih Kelas</label>
                <select id="filter_kelas" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Kelas --</option>
                    <?php foreach ($kelasDiajar as $k): ?>
                        <option value="<?= esc($k) ?>">Kelas <?= esc($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Step 2: Student Table -->
        <div id="student-table-container" style="display: none;">
            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 0 0 1.5rem 0;">
            <h4 style="font-family: var(--font-heading); font-size: 1rem; margin-bottom: 1rem; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-users"></i>
                <span>Daftar Siswa — <span id="label-mapel" style="color: var(--secondary);"></span> — <span id="label-kelas" style="color: var(--secondary);"></span></span>
            </h4>
            <div class="table-container">
                <div class="table-wrapper">
                    <table class="custom-table" id="siswa-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Nilai Tugas (30%)</th>
                                <th>Nilai UTS (30%)</th>
                                <th>Nilai UAS (40%)</th>
                                <th style="width: 120px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="siswa-tbody">
                            <tr id="loading-row">
                                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Memuat data siswa...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Empty state when no filter selected -->
        <div id="empty-state" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
            <i class="fa-solid fa-filter" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-light);"></i>
            <p style="font-size: 0.95rem; font-weight: 500;">Pilih <strong>Mata Pelajaran</strong> dan <strong>Kelas</strong> terlebih dahulu untuk menampilkan daftar siswa.</p>
        </div>
    </div>
</div>

<!-- Hidden Form for submission -->
<form id="nilai-form" action="<?= base_url('/nilai/store') ?>" method="post" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="nis" id="form-nis">
    <input type="hidden" name="mata_pelajaran" id="form-mapel">
    <input type="hidden" name="nilai_tugas" id="form-tugas">
    <input type="hidden" name="nilai_uts" id="form-uts">
    <input type="hidden" name="nilai_uas" id="form-uas">
</form>

<script>
const mappings = <?= json_encode($mappings) ?>;

const filterMapel = document.getElementById('filter_mapel');
const filterKelas = document.getElementById('filter_kelas');
const studentContainer = document.getElementById('student-table-container');
const emptyState = document.getElementById('empty-state');
const siswaTbody = document.getElementById('siswa-tbody');
const labelMapel = document.getElementById('label-mapel');
const labelKelas = document.getElementById('label-kelas');

const originalMapelOptions = Array.from(filterMapel.options).slice(1);
const originalKelasOptions = Array.from(filterKelas.options).slice(1);

function updateFilters() {
    const selectedMapel = filterMapel.value;
    const selectedKelas = filterKelas.value;

    // Filter Kelas options based on selected Mapel
    if (selectedMapel) {
        const validKelases = mappings
            .filter(m => m.mata_pelajaran === selectedMapel)
            .map(m => m.kelas);

        filterKelas.innerHTML = '<option value="" disabled selected>-- Pilih Kelas --</option>';
        let matchesSelected = false;
        originalKelasOptions.forEach(opt => {
            if (validKelases.includes(opt.value)) {
                const newOpt = opt.cloneNode(true);
                if (opt.value === selectedKelas) {
                    newOpt.selected = true;
                    matchesSelected = true;
                }
                filterKelas.appendChild(newOpt);
            }
        });
        if (selectedKelas && matchesSelected) {
            filterKelas.value = selectedKelas;
        }
    } else {
        filterKelas.innerHTML = '<option value="" disabled selected>-- Pilih Kelas --</option>';
        originalKelasOptions.forEach(opt => filterKelas.appendChild(opt.cloneNode(true)));
        if (selectedKelas) {
            filterKelas.value = selectedKelas;
        }
    }

    // Filter Mapel options based on selected Kelas
    if (selectedKelas) {
        const validMapels = mappings
            .filter(m => m.kelas === selectedKelas)
            .map(m => m.mata_pelajaran);

        filterMapel.innerHTML = '<option value="" disabled selected>-- Pilih Mata Pelajaran --</option>';
        let matchesSelected = false;
        originalMapelOptions.forEach(opt => {
            if (validMapels.includes(opt.value)) {
                const newOpt = opt.cloneNode(true);
                if (opt.value === selectedMapel) {
                    newOpt.selected = true;
                    matchesSelected = true;
                }
                filterMapel.appendChild(newOpt);
            }
        });
        if (selectedMapel && matchesSelected) {
            filterMapel.value = selectedMapel;
        }
    } else {
        filterMapel.innerHTML = '<option value="" disabled selected>-- Pilih Mata Pelajaran --</option>';
        originalMapelOptions.forEach(opt => filterMapel.appendChild(opt.cloneNode(true)));
        if (selectedMapel) {
            filterMapel.value = selectedMapel;
        }
    }
}

function loadStudents() {
    const mapel = filterMapel.value;
    const kelas = filterKelas.value;
    
    if (!mapel || !kelas) return;

    // Show table, hide empty state
    studentContainer.style.display = '';
    emptyState.style.display = 'none';
    labelMapel.textContent = mapel;
    labelKelas.textContent = 'Kelas ' + kelas;

    // Show loading
    siswaTbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data siswa...</td></tr>';

    fetch('<?= base_url('/nilai/siswa-by-kelas/') ?>' + kelas)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                siswaTbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Tidak ada siswa aktif di kelas ini.</td></tr>';
                return;
            }
            let html = '';
            data.forEach((s, i) => {
                html += `<tr data-nis="${s.nis}">
                    <td style="text-align: center; font-weight: 600;">${i + 1}</td>
                    <td style="font-weight: 600;">${s.nama}</td>
                    <td><span style="font-family: monospace; font-size: 0.85rem; color: var(--text-muted);">${s.nis}</span></td>
                    <td><input type="number" class="form-control input-tugas" value="0" min="0" max="100" step="0.01" style="width: 90px; padding: 0.4rem 0.6rem; font-size: 0.85rem;"></td>
                    <td><input type="number" class="form-control input-uts" value="0" min="0" max="100" step="0.01" style="width: 90px; padding: 0.4rem 0.6rem; font-size: 0.85rem;"></td>
                    <td><input type="number" class="form-control input-uas" value="0" min="0" max="100" step="0.01" style="width: 90px; padding: 0.4rem 0.6rem; font-size: 0.85rem;"></td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-primary btn-sm btn-simpan" onclick="simpanNilai(this)" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan</span>
                        </button>
                    </td>
                </tr>`;
            });
            siswaTbody.innerHTML = html;
        })
        .catch(() => {
            siswaTbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: var(--danger); padding: 2rem;">Gagal memuat data siswa.</td></tr>';
        });
}

filterMapel.addEventListener('change', () => {
    updateFilters();
    loadStudents();
});

filterKelas.addEventListener('change', () => {
    updateFilters();
    loadStudents();
});

function simpanNilai(btn) {
    const row = btn.closest('tr');
    const nis = row.dataset.nis;
    const tugas = row.querySelector('.input-tugas').value;
    const uts = row.querySelector('.input-uts').value;
    const uas = row.querySelector('.input-uas').value;
    const mapel = filterMapel.value;

    if (!mapel) {
        alert('Pilih mata pelajaran terlebih dahulu.');
        return;
    }

    document.getElementById('form-nis').value = nis;
    document.getElementById('form-mapel').value = mapel;
    document.getElementById('form-tugas').value = tugas;
    document.getElementById('form-uts').value = uts;
    document.getElementById('form-uas').value = uas;
    document.getElementById('nilai-form').submit();
}
</script>

<?= $this->endSection() ?>
