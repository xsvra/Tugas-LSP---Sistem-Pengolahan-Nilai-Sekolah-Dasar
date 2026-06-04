<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container" style="max-width: 100%; width: 100%;">
    <div class="form-card" style="width: 100%;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
            <a href="<?= base_url('/siswa') ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="section-title" style="margin: 0;">Tambah Siswa Baru</h3>
        </div>

        <form action="<?= base_url('/siswa/store') ?>" method="post">
            <?= csrf_field() ?>

            <!-- NIS Field -->
            <div class="form-group">
                <label for="nis">Nomor Induk Siswa (NIS)</label>
                <input type="text" name="nis" id="nis" class="form-control <?= (isset($session_errors['nis'])) ? 'is-invalid' : '' ?>" value="<?= old('nis', $nextNis) ?>" placeholder="Contoh: 101 atau 2026A001" required>
                <?php if (isset($session_errors['nis'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['nis'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Account Credentials -->
            <h4 style="font-family: var(--font-heading); font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 1rem; color: var(--primary);">Kredensial Akun Siswa</h4>

            <!-- Username -->
            <div class="form-group">
                <label for="username">Username Akun</label>
                <input type="text" name="username" id="username" class="form-control <?= (isset($session_errors['username'])) ? 'is-invalid' : '' ?>" value="<?= old('username') ?>" placeholder="Masukkan username login siswa" required>
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
            <h4 style="font-family: var(--font-heading); font-size: 1.1rem; margin-bottom: 1rem; color: var(--primary);">Informasi Akademik</h4>



            <!-- Kelas Field -->
            <div class="form-group">
                <label for="kelas">Kelas</label>
                <select name="kelas" id="kelas" class="form-control <?= (isset($session_errors['kelas'])) ? 'is-invalid' : '' ?>" required>
                    <option value="" disabled selected>-- Pilih Kelas --</option>
                    <?php for($i=1; $i<=6; $i++): ?>
                        <option value="<?= $i ?>" <?= old('kelas') == $i ? 'selected' : '' ?>>Kelas <?= $i ?></option>
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
                    <option value="Aktif" <?= old('status_siswa') === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="Lulus" <?= old('status_siswa') === 'Lulus' ? 'selected' : '' ?>>Lulus</option>
                    <option value="Pindah" <?= old('status_siswa') === 'Pindah' ? 'selected' : '' ?>>Pindah</option>
                    <option value="Keluar" <?= old('status_siswa') === 'Keluar' ? 'selected' : '' ?>>Keluar</option>
                </select>
                <?php if (isset($session_errors['status_siswa'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['status_siswa'] ?></div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="<?= base_url('/siswa') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Siswa</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
