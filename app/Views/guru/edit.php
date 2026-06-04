<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container" style="max-width: 100%; width: 100%;">
    <div class="form-card" style="width: 100%;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
            <a href="<?= base_url('/guru') ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="section-title" style="margin: 0;">Edit Data Guru</h3>
        </div>

        <form action="<?= base_url('/guru/update/' . $guru['id_guru']) ?>" method="post">
            <?= csrf_field() ?>

            <!-- ID Guru Field (Disabled) -->
            <div class="form-group">
                <label for="id_guru">ID Guru / NIP</label>
                <input type="text" id="id_guru" class="form-control" value="<?= esc($guru['id_guru']) ?>" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
            </div>

            <!-- NIK Field -->
            <div class="form-group">
                <label for="nik">NIK</label>
                <input type="text" name="nik" id="nik" class="form-control <?= (isset($session_errors['nik'])) ? 'is-invalid' : '' ?>" value="<?= old('nik', $guru['nik']) ?>" placeholder="Contoh: 3201234567890123">
                <?php if (isset($session_errors['nik'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['nik'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Nama Guru Field -->
            <div class="form-group">
                <label for="nama_guru">Nama Lengkap Guru</label>
                <input type="text" name="nama_guru" id="nama_guru" class="form-control <?= (isset($session_errors['nama_guru'])) ? 'is-invalid' : '' ?>" value="<?= old('nama_guru', $guru['nama_guru']) ?>" placeholder="Contoh: Budi Santoso, S.Pd." required>
                <?php if (isset($session_errors['nama_guru'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['nama_guru'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Jenis Kelamin Field -->
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control <?= (isset($session_errors['jenis_kelamin'])) ? 'is-invalid' : '' ?>" required>
                    <option value="L" <?= old('jenis_kelamin', $guru['jenis_kelamin']) === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= old('jenis_kelamin', $guru['jenis_kelamin']) === 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
                <?php if (isset($session_errors['jenis_kelamin'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['jenis_kelamin'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Mata Pelajaran Field -->
            <div class="form-group">
                <label for="mata_pelajaran">Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" id="mata_pelajaran" class="form-control <?= (isset($session_errors['mata_pelajaran'])) ? 'is-invalid' : '' ?>" value="<?= old('mata_pelajaran', $guru['mata_pelajaran']) ?>" placeholder="Contoh: Matematika" required>
                <?php if (isset($session_errors['mata_pelajaran'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['mata_pelajaran'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Status Kepegawaian Field -->
            <div class="form-group">
                <label for="status_kepegawaian">Status Kepegawaian</label>
                <select name="status_kepegawaian" id="status_kepegawaian" class="form-control <?= (isset($session_errors['status_kepegawaian'])) ? 'is-invalid' : '' ?>" required>
                    <option value="PNS" <?= old('status_kepegawaian', $guru['status_kepegawaian']) === 'PNS' ? 'selected' : '' ?>>PNS (Pegawai Negeri Sipil)</option>
                    <option value="Honorer" <?= old('status_kepegawaian', $guru['status_kepegawaian']) === 'Honorer' ? 'selected' : '' ?>>Honorer</option>
                    <option value="P3K" <?= old('status_kepegawaian', $guru['status_kepegawaian']) === 'P3K' ? 'selected' : '' ?>>P3k</option>
                </select>
                <?php if (isset($session_errors['status_kepegawaian'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['status_kepegawaian'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Tanggal Masuk Field -->
            <div class="form-group">
                <label for="tanggal_masuk">Tanggal Masuk Kerja</label>
                <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control <?= (isset($session_errors['tanggal_masuk'])) ? 'is-invalid' : '' ?>" value="<?= old('tanggal_masuk', $guru['tanggal_masuk']) ?>" required>
                <?php if (isset($session_errors['tanggal_masuk'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['tanggal_masuk'] ?></div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="<?= base_url('/guru') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
