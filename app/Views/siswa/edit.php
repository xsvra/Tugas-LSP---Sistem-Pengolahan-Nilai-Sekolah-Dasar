<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container" style="max-width: 100%; width: 100%;">
    <div class="form-card" style="width: 100%;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
            <a href="<?= base_url('/siswa') ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="section-title" style="margin: 0;">Edit Data Siswa</h3>
        </div>

        <form action="<?= base_url('/siswa/update/' . $siswa['nis']) ?>" method="post">
            <?= csrf_field() ?>

            <!-- NIS Field (Disabled) -->
            <div class="form-group">
                <label for="nis">Nomor Induk Siswa (NIS)</label>
                <input type="text" id="nis" class="form-control" value="<?= esc($siswa['nis']) ?>" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
            </div>

            <!-- Nama Field -->
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="form-control <?= (isset($session_errors['nama'])) ? 'is-invalid' : '' ?>" value="<?= old('nama', $siswa['nama']) ?>" placeholder="Contoh: Ani Wijaya" required>
                <?php if (isset($session_errors['nama'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['nama'] ?></div>
                <?php endif; ?>
            </div>


            <!-- Kelas Field -->
            <div class="form-group">
                <label for="kelas">Kelas</label>
                <select name="kelas" id="kelas" class="form-control <?= (isset($session_errors['kelas'])) ? 'is-invalid' : '' ?>" required>
                    <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= old('kelas', $siswa['kelas']) == $i ? 'selected' : '' ?>>Kelas <?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <?php if (isset($session_errors['kelas'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['kelas'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Status Siswa Field -->
            <div class="form-group">
                <label for="status_siswa">Status Siswa</label>
                <select name="status_siswa" id="status_siswa" class="form-control <?= (isset($session_errors['status_siswa'])) ? 'is-invalid' : '' ?>" required>
                    <option value="Aktif" <?= old('status_siswa', $siswa['status_siswa']) === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Lulus" <?= old('status_siswa', $siswa['status_siswa']) === 'Lulus' ? 'selected' : '' ?>>Lulus</option>
                    <option value="Pindah" <?= old('status_siswa', $siswa['status_siswa']) === 'Pindah' ? 'selected' : '' ?>>Pindah</option>
                    <option value="Keluar" <?= old('status_siswa', $siswa['status_siswa']) === 'Keluar' ? 'selected' : '' ?>>Keluar</option>
                </select>
                <?php if (isset($session_errors['status_siswa'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['status_siswa'] ?></div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="<?= base_url('/siswa') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
