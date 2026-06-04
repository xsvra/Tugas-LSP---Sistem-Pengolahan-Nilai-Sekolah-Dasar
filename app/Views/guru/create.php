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

            <!-- Mata Pelajaran Field -->
            <div class="form-group">
                <label for="mata_pelajaran">Mata Pelajaran Diampu</label>
                <input type="text" name="mata_pelajaran" id="mata_pelajaran" class="form-control <?= (isset($session_errors['mata_pelajaran'])) ? 'is-invalid' : '' ?>" value="<?= old('mata_pelajaran') ?>" placeholder="Contoh: Matematika" required>
                <?php if (isset($session_errors['mata_pelajaran'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['mata_pelajaran'] ?></div>
                <?php endif; ?>
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

<?= $this->endSection() ?>
