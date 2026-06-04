<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div style="display: flex; flex-direction: column; gap: 1rem;">
    <div class="section-header">
        <h3 class="section-title">Daftar Guru</h3>
        <a href="<?= base_url('/guru/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Guru</span>
        </a>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ID Guru</th>
                        <th>Nama Guru</th>
                        <th>Mata Pelajaran</th>
                        <th>Waktu Ditambahkan</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($guru)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data guru. Silakan klik Tambah Guru.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($guru as $g): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--primary);"><?= esc($g['id_guru']) ?></td>
                                <td style="font-weight: 600;"><?= esc($g['nama_guru']) ?></td>
                                <td><span class="badge badge-success"><?= esc($g['mata_pelajaran']) ?></span></td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);"><?= esc($g['created_at']) ?></td>
                                <td>
                                    <div class="btn-group" style="justify-content: center;">
                                        <a href="<?= base_url('/guru/edit/' . $g['id_guru']) ?>" class="btn btn-secondary btn-sm" title="Edit">
                                            <i class="fa-solid fa-pen-to-square" style="color: var(--secondary);"></i>
                                        </a>
                                        <a href="<?= base_url('/guru/delete/' . $g['id_guru']) ?>" class="btn btn-secondary btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus guru ini? Semua data nilai yang diinput oleh guru ini juga akan dihapus.');">
                                            <i class="fa-solid fa-trash-can" style="color: var(--danger);"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
