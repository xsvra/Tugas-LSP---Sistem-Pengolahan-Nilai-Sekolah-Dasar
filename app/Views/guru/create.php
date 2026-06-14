<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container" style="max-width: 100%; width: 100%;">
    <div class="form-card" style="width: 100%;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
            <a href="<?= base_url('/guru') ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="section-title" style="margin: 0;">Tambah Guru Baru</h3>
        </div>

        <form action="<?= base_url('/guru/store') ?>" method="post">
            <?= csrf_field() ?>

            <!-- ID Guru Field (Read-only / Auto-increment hint) -->
            <div class="form-group">
                <label for="id_guru">ID Guru / NIP</label>
                <input type="text" id="id_guru" class="form-control" value="<?= esc($nextId) ?> (Otomatis)" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
            </div>

            <!-- Account Credentials -->
            <h4 style="font-family: var(--font-heading); font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 1rem; color: var(--primary);">Kredensial Akun Guru</h4>

            <!-- Username -->
            <div class="form-group">
                <label for="username">Username Akun</label>
                <input type="text" name="username" id="username" class="form-control <?= (isset($session_errors['username'])) ? 'is-invalid' : '' ?>" value="<?= old('username') ?>" placeholder="Masukkan username login guru" required>
                <?php if (isset($session_errors['username'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['username'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password (min. 6 karakter)</label>
                <input type="password" name="password" id="password" class="form-control <?= (isset($session_errors['password'])) ? 'is-invalid' : '' ?>" placeholder="Masukkan password" required>
                <?php if (isset($session_errors['password'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['password'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Konfirmasi Password -->
            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password</label>
                <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control <?= (isset($session_errors['konfirmasi_password'])) ? 'is-invalid' : '' ?>" placeholder="Konfirmasi password" required>
                <?php if (isset($session_errors['konfirmasi_password'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['konfirmasi_password'] ?></div>
                <?php endif; ?>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 2rem 0;">
            <h4 style="font-family: var(--font-heading); font-size: 1.1rem; margin-bottom: 1rem; color: var(--primary);">Informasi Kepegawaian</h4>

            <!-- Pemetaan Mapel & Kelas Diajar -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="font-weight: 600; font-size: 1rem; color: var(--text-main); margin-bottom: 0.5rem; display: block;">Pemetaan Mata Pelajaran & Kelas yang Diajar</label>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: -0.25rem; margin-bottom: 1rem;">Tentukan mata pelajaran yang diajar untuk setiap kelas secara spesifik (Contoh: IPA di Kelas 2, PJOK di Kelas 4).</p>

                <?php if (isset($session_errors['mappings'])): ?>
                    <div class="alert alert-danger" style="margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: var(--border-radius-md); font-size: 0.85rem; color: var(--danger); background-color: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2);">
                        <i class="fa-solid fa-circle-exclamation" style="margin-right: 0.5rem;"></i> <?= $session_errors['mappings'] ?>
                    </div>
                <?php endif; ?>

                <div id="mapping-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php 
                    $oldMapels = old('mapped_mapel') ?: [];
                    $oldKelases = old('mapped_kelas') ?: [];
                    $mapelOptions = ['Matematika', 'IPA', 'Bahasa Indonesia', 'PJOK', 'PKN'];
                    
                    // If no old mapping, output one empty row as default
                    if (empty($oldMapels)) {
                        $oldMapels = [''];
                        $oldKelases = [''];
                    }

                    for ($i = 0; $i < count($oldMapels); $i++):
                        $selectedMapel = $oldMapels[$i];
                        $selectedKelas = $oldKelases[$i];
                    ?>
                        <div class="mapping-row" style="display: flex; align-items: center; gap: 1rem; background: rgba(0,0,0,0.02); padding: 0.75rem; border-radius: var(--border-radius-md); border: 1px solid var(--border-color);">
                            <div style="flex: 1;">
                                <select name="mapped_mapel[]" class="form-control" required style="width: 100%;">
                                    <option value="" disabled <?= empty($selectedMapel) ? 'selected' : '' ?>>-- Pilih Mapel --</option>
                                    <?php foreach ($mapelOptions as $mo): ?>
                                        <option value="<?= $mo ?>" <?= $selectedMapel === $mo ? 'selected' : '' ?>><?= $mo ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="flex: 1;">
                                <select name="mapped_kelas[]" class="form-control" required style="width: 100%;">
                                    <option value="" disabled <?= empty($selectedKelas) ? 'selected' : '' ?>>-- Pilih Kelas --</option>
                                    <?php for ($k = 1; $k <= 6; $k++): ?>
                                        <option value="<?= $k ?>" <?= (string)$selectedKelas === (string)$k ? 'selected' : '' ?>>Kelas <?= $k ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm btn-remove-mapping" style="padding: 0.5rem; background-color: transparent; border: 1px solid var(--border-color); color: var(--danger); width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border-radius: var(--border-radius-sm);" onclick="removeMappingRow(this)">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    <?php endfor; ?>
                </div>

                <button type="button" class="btn btn-secondary btn-sm" id="btn-add-mapping" style="margin-top: 1rem; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: var(--border-radius-md);">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Pemetaan</span>
                </button>
            </div>

            <!-- Status Kepegawaian Field -->
            <div class="form-group">
                <label for="status_kepegawaian">Status Kepegawaian</label>
                <select name="status_kepegawaian" id="status_kepegawaian" class="form-control <?= (isset($session_errors['status_kepegawaian'])) ? 'is-invalid' : '' ?>" required>
                    <option value="" disabled selected>-- Pilih Status Kepegawaian --</option>
                    <option value="PNS" <?= old('status_kepegawaian') === 'PNS' ? 'selected' : '' ?>>PNS (Pegawai Negeri Sipil)</option>
                    <option value="Honorer" <?= old('status_kepegawaian') === 'Honorer' ? 'selected' : '' ?>>Honorer</option>
                    <option value="P3K" <?= old('status_kepegawaian') === 'P3K' ? 'selected' : '' ?>>P3k</option>
                </select>
                <?php if (isset($session_errors['status_kepegawaian'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['status_kepegawaian'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Tanggal Masuk Field -->
            <div class="form-group">
                <label for="tanggal_masuk">Tanggal Masuk Kerja</label>
                <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control <?= (isset($session_errors['tanggal_masuk'])) ? 'is-invalid' : '' ?>" value="<?= old('tanggal_masuk') ?>" required>
                <?php if (isset($session_errors['tanggal_masuk'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['tanggal_masuk'] ?></div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2.5rem;">
                <a href="<?= base_url('/guru') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Guru</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function removeMappingRow(btn) {
    const container = document.getElementById('mapping-container');
    if (container.querySelectorAll('.mapping-row').length > 1) {
        btn.closest('.mapping-row').remove();
    } else {
        alert('Minimal harus ada satu pemetaan mata pelajaran & kelas.');
    }
}

document.getElementById('btn-add-mapping').addEventListener('click', function() {
    const container = document.getElementById('mapping-container');
    const firstRow = container.querySelector('.mapping-row');
    const newRow = firstRow.cloneNode(true);
    
    // Reset selections
    newRow.querySelectorAll('select').forEach(sel => {
        sel.value = "";
    });
    
    container.appendChild(newRow);
});
</script>

<?= $this->endSection() ?>

