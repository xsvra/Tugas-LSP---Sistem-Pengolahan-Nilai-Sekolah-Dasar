<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container" style="max-width: 100%; width: 100%;">
    <div class="form-card" style="width: 100%;">
        <h3 class="section-title" style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-address-card" style="color: var(--primary);"></i>
            <span>Data Diri Anda (Siswa)</span>
        </h3>

        <?php if ($editMode): ?>
            <!-- Mode Edit: Menampilkan Form Input -->
            <form action="<?= base_url('/profil/siswa/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
                    
                    <!-- Left Column: Photo & Basic status -->
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem; border-right: 1px solid rgba(0,0,0,0.05); padding-right: 2rem;">
                        <div style="position: relative; width: 180px; height: 180px; border-radius: 50%; overflow: hidden; background: #e2e8f0; border: 3px solid var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center;">
                            <?php if (!empty($siswa['foto']) && file_exists(ROOTPATH . 'public/uploads/foto/' . $siswa['foto'])): ?>
                                <img src="<?= base_url('uploads/foto/' . $siswa['foto']) ?>" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <i class="fa-solid fa-user-graduate" style="font-size: 5rem; color: #a0aec0;"></i>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group" style="width: 100%;">
                            <label for="foto" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; justify-content: center; width: 100%;">
                                <i class="fa-solid fa-camera"></i>
                                <span>Unggah Foto</span>
                            </label>
                            <input type="file" name="foto" id="foto" accept="image/*" style="display: none;" onchange="previewImage(event);">
                            <small style="display: block; color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem;">Format JPG/PNG, Maks. 2MB</small>
                        </div>

                        <div style="width: 100%; text-align: left; background: rgba(99, 102, 241, 0.03); border-radius: var(--border-radius-md); padding: 1rem; border: 1px dashed rgba(99, 102, 241, 0.2);">
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--primary);">Informasi Akademik</h4>
                            <div style="font-size: 0.8rem; display: flex; flex-direction: column; gap: 0.25rem;">
                                <div><strong>NIS:</strong> <?= esc($siswa['nis']) ?></div>
                                <div><strong>NISN:</strong> <?= esc($siswa['nisn']) ?></div>
                                <div><strong>Status:</strong> <span class="badge badge-success"><?= esc($siswa['status_siswa'] ?: 'Aktif') ?></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Personal Details Form -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        
                        <!-- Nama Siswa -->
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama" class="form-control <?= (isset($session_errors['nama'])) ? 'is-invalid' : '' ?>" value="<?= old('nama', $siswa['nama']) ?>" placeholder="Nama Lengkap" required>
                            <?php if (isset($session_errors['nama'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['nama'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="form-group">
                            <label for="jenis_kelamin">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="jenis_kelamin" class="form-control <?= (isset($session_errors['jenis_kelamin'])) ? 'is-invalid' : '' ?>" required>
                                <option value="L" <?= old('jenis_kelamin', $siswa['jenis_kelamin']) === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= old('jenis_kelamin', $siswa['jenis_kelamin']) === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                            <?php if (isset($session_errors['jenis_kelamin'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['jenis_kelamin'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Kelas (Read-only) -->
                        <div class="form-group">
                            <label for="kelas">Kelas</label>
                            <input type="text" class="form-control" value="Kelas <?= esc($siswa['kelas']) ?>" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
                            <input type="hidden" name="kelas" value="<?= esc($siswa['kelas']) ?>">
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="form-group">
                            <label for="tempat_lahir">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control <?= (isset($session_errors['tempat_lahir'])) ? 'is-invalid' : '' ?>" value="<?= old('tempat_lahir', $siswa['tempat_lahir']) ?>" placeholder="Tempat Lahir">
                            <?php if (isset($session_errors['tempat_lahir'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['tempat_lahir'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control <?= (isset($session_errors['tanggal_lahir'])) ? 'is-invalid' : '' ?>" value="<?= old('tanggal_lahir', $siswa['tanggal_lahir']) ?>" required>
                            <?php if (isset($session_errors['tanggal_lahir'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['tanggal_lahir'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Tahun Masuk -->
                        <div class="form-group">
                            <label for="tahun_masuk">Tahun Masuk</label>
                            <input type="text" name="tahun_masuk" id="tahun_masuk" class="form-control <?= (isset($session_errors['tahun_masuk'])) ? 'is-invalid' : '' ?>" value="<?= old('tahun_masuk', $siswa['tahun_masuk']) ?>" placeholder="Contoh: 2025" required>
                            <?php if (isset($session_errors['tahun_masuk'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['tahun_masuk'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Status Siswa (Read-only) -->
                        <div class="form-group">
                            <label for="status_siswa">Status Siswa</label>
                            <input type="text" class="form-control" value="<?= esc($siswa['status_siswa'] ?: 'Aktif') ?>" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
                            <input type="hidden" name="status_siswa" value="<?= esc($siswa['status_siswa'] ?: 'Aktif') ?>">
                        </div>

                        <!-- Nama Wali -->
                        <div class="form-group">
                            <label for="nama_wali">Nama Wali</label>
                            <input type="text" name="nama_wali" id="nama_wali" class="form-control <?= (isset($session_errors['nama_wali'])) ? 'is-invalid' : '' ?>" value="<?= old('nama_wali', $siswa['nama_wali']) ?>" placeholder="Nama Orang Tua / Wali">
                            <?php if (isset($session_errors['nama_wali'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['nama_wali'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- No HP Wali -->
                        <div class="form-group">
                            <label for="no_hp_wali">No. HP Wali</label>
                            <input type="text" name="no_hp_wali" id="no_hp_wali" class="form-control <?= (isset($session_errors['no_hp_wali'])) ? 'is-invalid' : '' ?>" value="<?= old('no_hp_wali', $siswa['no_hp_wali']) ?>" placeholder="No HP Wali">
                            <?php if (isset($session_errors['no_hp_wali'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['no_hp_wali'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Alamat -->
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="alamat">Alamat Lengkap</label>
                            <textarea name="alamat" id="alamat" class="form-control <?= (isset($session_errors['alamat'])) ? 'is-invalid' : '' ?>" rows="3" placeholder="Alamat Lengkap Siswa"><?= old('alamat', $siswa['alamat']) ?></textarea>
                            <?php if (isset($session_errors['alamat'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['alamat'] ?></div>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2.5rem; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 1.5rem;">
                    <a href="<?= base_url('/profil/siswa') ?>" class="btn btn-secondary" style="padding: 0.75rem 1.5rem;">Batal</a>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Data Diri</span>
                    </button>
                </div>
            </form>

        <?php else: ?>
            <!-- Mode Summary: Menampilkan Rangkuman Profil -->
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
                
                <!-- Left Column: Photo & basic info -->
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1.5rem; border-right: 1px solid rgba(0,0,0,0.05); padding-right: 2rem;">
                    <div style="width: 180px; height: 180px; border-radius: 50%; overflow: hidden; background: #e2e8f0; border: 3px solid var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($siswa['foto']) && file_exists(ROOTPATH . 'public/uploads/foto/' . $siswa['foto'])): ?>
                            <img src="<?= base_url('uploads/foto/' . $siswa['foto']) ?>" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fa-solid fa-user-graduate" style="font-size: 5rem; color: #a0aec0;"></i>
                        <?php endif; ?>
                    </div>

                    <div style="width: 100%; text-align: left; background: rgba(99, 102, 241, 0.03); border-radius: var(--border-radius-md); padding: 1.25rem; border: 1px solid rgba(99, 102, 241, 0.15);">
                        <h4 style="margin: 0 0 0.75rem 0; font-size: 0.95rem; color: var(--primary); font-family: var(--font-heading); font-weight: 700; border-bottom: 1px solid rgba(99,102,241,0.15); padding-bottom: 0.5rem;">Informasi Akademik</h4>
                        <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            <div><strong style="color: var(--text-muted);">NIS:</strong> <span style="font-weight: 600; color: var(--text-main);"><?= esc($siswa['nis']) ?></span></div>
                            <div><strong style="color: var(--text-muted);">NISN:</strong> <span style="font-weight: 600; color: var(--text-main);"><?= esc($siswa['nisn']) ?></span></div>
                            <div><strong style="color: var(--text-muted);">Status Keaktifan:</strong> <span class="badge badge-success"><?= esc($siswa['status_siswa'] ?: 'Aktif') ?></span></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Personal & Wali details -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <!-- Box Data Pribadi -->
                    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--border-radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                        <h4 style="margin: 0 0 1.25rem 0; font-size: 1.05rem; color: var(--primary-dark); font-family: var(--font-heading); font-weight: 700; border-bottom: 1.5px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-user" style="font-size: 0.95rem;"></i>
                            <span>Data Pribadi Siswa</span>
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; font-size: 0.925rem;">
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Nama Lengkap</span>
                                <span style="font-weight: 700; color: var(--text-main); font-size: 1rem;"><?= esc($siswa['nama']) ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Jenis Kelamin</span>
                                <span style="font-weight: 600; color: var(--text-main);"><?= $siswa['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki' ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Tempat, Tanggal Lahir</span>
                                <span style="font-weight: 600; color: var(--text-main);">
                                    <?= (!empty($siswa['tempat_lahir']) || !empty($siswa['tanggal_lahir'])) ? esc($siswa['tempat_lahir']) . ', ' . (!empty($siswa['tanggal_lahir']) ? date('d-m-Y', strtotime($siswa['tanggal_lahir'])) : '') : '<em style="color: var(--text-light);">Belum diisi</em>' ?>
                                </span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Kelas Aktif</span>
                                <span class="badge badge-primary" style="font-size: 0.85rem; padding: 0.25rem 0.75rem;">Kelas <?= esc($siswa['kelas']) ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Tahun Masuk</span>
                                <span style="font-weight: 600; color: var(--text-main);"><?= !empty($siswa['tahun_masuk']) ? esc($siswa['tahun_masuk']) : '<em style="color: var(--text-light);">Belum diisi</em>' ?></span>
                            </div>
                            <div>
                                <!-- Spacer to balance the grid row -->
                            </div>
                            <div style="grid-column: span 2;">
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Alamat Rumah</span>
                                <span style="font-weight: 500; color: var(--text-main); line-height: 1.4; display: block;">
                                    <?= !empty($siswa['alamat']) ? nl2br(esc($siswa['alamat'])) : '<em style="color: var(--text-light);">Belum diisi</em>' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Box Data Wali -->
                    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--border-radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                        <h4 style="margin: 0 0 1.25rem 0; font-size: 1.05rem; color: var(--secondary); font-family: var(--font-heading); font-weight: 700; border-bottom: 1.5px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-people-roof" style="font-size: 0.95rem;"></i>
                            <span>Informasi Orang Tua / Wali</span>
                        </h4>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; font-size: 0.925rem;">
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Nama Wali / Orang Tua</span>
                                <span style="font-weight: 600; color: var(--text-main);"><?= !empty($siswa['nama_wali']) ? esc($siswa['nama_wali']) : '<em style="color: var(--text-light);">Belum diisi</em>' ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">No. Handphone Wali</span>
                                <span style="font-weight: 600; color: var(--text-main); font-family: monospace; font-size: 0.95rem;"><?= !empty($siswa['no_hp_wali']) ? esc($siswa['no_hp_wali']) : '<em style="color: var(--text-light);">Belum diisi</em>' ?></span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2.5rem; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 1.5rem;">
                <a href="?edit=true" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Ubah Data Diri</span>
                </a>
            </div>

        <?php endif; ?>
    </div>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const container = event.target.parentElement.previousElementSibling;
        let img = container.querySelector('img');
        if (!img) {
            container.innerHTML = '';
            img = document.createElement('img');
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            container.appendChild(img);
        }
        img.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

<?= $this->endSection() ?>
