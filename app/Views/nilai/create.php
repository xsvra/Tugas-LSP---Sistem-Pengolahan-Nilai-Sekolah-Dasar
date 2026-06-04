<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container">
    <div class="form-card">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
            <a href="<?= base_url('/nilai') ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="section-title" style="margin: 0;">Input Nilai Siswa Baru</h3>
        </div>

        <form action="<?= base_url('/nilai/store') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Siswa Select -->
            <div class="form-group">
                <label for="nis">Pilih Siswa</label>
                <select name="nis" id="nis" class="form-control <?= (isset($session_errors['nis'])) ? 'is-invalid' : '' ?>" required>
                    <option value="" disabled selected>-- Pilih Siswa --</option>
                    <?php foreach ($siswa as $s): ?>
                        <option value="<?= $s['nis'] ?>" <?= old('nis') == $s['nis'] ? 'selected' : '' ?>>
                            <?= esc($s['nama']) ?> (NIS: <?= esc($s['nis']) ?> - Kelas: <?= esc($s['kelas']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($session_errors['nis'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['nis'] ?></div>
                <?php endif; ?>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <!-- Nilai Tugas -->
                <div class="form-group">
                    <label for="nilai_tugas">Nilai Tugas (30%)</label>
                    <input type="number" name="nilai_tugas" id="nilai_tugas" class="form-control <?= (isset($session_errors['nilai_tugas'])) ? 'is-invalid' : '' ?>" value="<?= old('nilai_tugas', '0') ?>" min="0" max="100" step="0.01" required>
                    <?php if (isset($session_errors['nilai_tugas'])): ?>
                        <div class="invalid-feedback"><?= $session_errors['nilai_tugas'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Nilai UTS -->
                <div class="form-group">
                    <label for="nilai_uts">Nilai UTS (30%)</label>
                    <input type="number" name="nilai_uts" id="nilai_uts" class="form-control <?= (isset($session_errors['nilai_uts'])) ? 'is-invalid' : '' ?>" value="<?= old('nilai_uts', '0') ?>" min="0" max="100" step="0.01" required>
                    <?php if (isset($session_errors['nilai_uts'])): ?>
                        <div class="invalid-feedback"><?= $session_errors['nilai_uts'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Nilai UAS -->
                <div class="form-group">
                    <label for="nilai_uas">Nilai UAS (40%)</label>
                    <input type="number" name="nilai_uas" id="nilai_uas" class="form-control <?= (isset($session_errors['nilai_uas'])) ? 'is-invalid' : '' ?>" value="<?= old('nilai_uas', '0') ?>" min="0" max="100" step="0.01" required>
                    <?php if (isset($session_errors['nilai_uas'])): ?>
                        <div class="invalid-feedback"><?= $session_errors['nilai_uas'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="<?= base_url('/nilai') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Nilai</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
