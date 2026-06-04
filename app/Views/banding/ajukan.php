<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container">
    <div class="form-card">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
            <a href="<?= base_url('/laporan/rapor/' . session()->get('ref_id')) ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fa-solid fa-arrow-left"></i></a>
            <h3 class="section-title" style="margin: 0;">Ajukan Banding Nilai</h3>
        </div>

        <div style="background-color: var(--primary-light); padding: 1.25rem; border-radius: var(--border-radius-md); border: 1px solid rgba(99, 102, 241, 0.1); margin-bottom: 2rem;">
            <h4 style="font-family: var(--font-heading); color: var(--primary-dark); font-size: 1rem; margin-bottom: 0.5rem;">Informasi Nilai yang Diajukan:</h4>
            <table style="width: 100%; font-size: 0.9rem; color: var(--text-main); border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.25rem 0; width: 140px; font-weight: 600;">Mata Pelajaran:</td>
                    <td style="padding: 0.25rem 0;"><?= esc($nilai['mata_pelajaran']) ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.25rem 0; font-weight: 600;">Guru Pengampu:</td>
                    <td style="padding: 0.25rem 0;"><?= esc($nilai['nama_guru']) ?></td>
                </tr>
                <tr>
                    <td style="padding: 0.25rem 0; font-weight: 600;">Nilai Akhir:</td>
                    <td style="padding: 0.25rem 0; font-weight: 700; color: var(--danger);"><?= number_format($nilai['nilai_akhir'], 2) ?> (Tugas: <?= number_format($nilai['nilai_tugas'], 1) ?>, UTS: <?= number_format($nilai['nilai_uts'], 1) ?>, UAS: <?= number_format($nilai['nilai_uas'], 1) ?>)</td>
                </tr>
            </table>
        </div>

        <form action="<?= base_url('/banding/ajukan/process/' . $nilai['id']) ?>" method="post">
            <?= csrf_field() ?>

            <!-- Alasan Banding -->
            <div class="form-group">
                <label for="alasan">Alasan Pengajuan Banding</label>
                <textarea name="alasan" id="alasan" class="form-control <?= (isset($session_errors['alasan'])) ? 'is-invalid' : '' ?>" rows="5" placeholder="Tuliskan alasan Anda mengapa nilai ini tidak sesuai (contoh: Nilai Tugas belum diinput guru, atau ada kesalahan input nilai UAS)..." required><?= old('alasan') ?></textarea>
                <?php if (isset($session_errors['alasan'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['alasan'] ?></div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                <a href="<?= base_url('/laporan/rapor/' . session()->get('ref_id')) ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Pengajuan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
