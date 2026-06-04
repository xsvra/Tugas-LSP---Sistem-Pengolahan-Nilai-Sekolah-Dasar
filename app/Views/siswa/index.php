<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div style="display: flex; flex-direction: column; gap: 1rem;">
    <div class="section-header">
        <h3 class="section-title">Daftar Siswa</h3>
        <a href="<?= base_url('/siswa/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Siswa</span>
        </a>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>Kelas</th>
                        <th>Waktu Ditambahkan</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($siswa)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data siswa. Silakan klik Tambah Siswa.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($siswa as $s): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--primary);"><?= esc($s['nis']) ?></td>
                                <td style="font-weight: 600;"><?= esc($s['nama']) ?></td>
                                <td><span class="badge badge-primary"><?= esc($s['kelas']) ?></span></td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);"><?= esc($s['created_at']) ?></td>
                                <td>
                                    <div class="btn-group" style="justify-content: center;">
                                        <a href="<?= base_url('/siswa/edit/' . $s['nis']) ?>" class="btn btn-secondary btn-sm" title="Edit">
                                            <i class="fa-solid fa-pen-to-square" style="color: var(--secondary);"></i>
                                        </a>
                                        <a href="<?= base_url('/siswa/delete/' . $s['nis']) ?>" class="btn btn-secondary btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Semua data nilai siswa ini juga akan dihapus.');">
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
