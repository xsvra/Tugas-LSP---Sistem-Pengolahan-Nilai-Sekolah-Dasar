<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php 
$session_errors = session()->getFlashdata('errors');
?>

<div class="form-container" style="max-width: 100%; width: 100%;">
    <div class="form-card" style="width: 100%;">
        <h3 class="section-title" style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-chalkboard-user" style="color: var(--primary);"></i>
            <span>Data Diri Anda (Guru)</span>
        </h3>

        <?php if ($editMode): ?>
            <!-- Mode Edit: Menampilkan Form Input -->
            <form action="<?= base_url('/profil/guru/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
                    
                    <!-- Left Column: Photo & Basic status -->
                    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem; border-right: 1px solid rgba(0,0,0,0.05); padding-right: 2rem;">
                        <div style="position: relative; width: 180px; height: 180px; border-radius: 50%; overflow: hidden; background: #e2e8f0; border: 3px solid var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center;">
                            <?php if (!empty($guru['foto']) && file_exists(ROOTPATH . 'public/uploads/foto/' . $guru['foto'])): ?>
                                <img src="<?= base_url('uploads/foto/' . $guru['foto']) ?>" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <i class="fa-solid fa-user-tie" style="font-size: 5rem; color: #a0aec0;"></i>
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
                            <h4 style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: var(--primary);">Informasi Kepegawaian</h4>
                            <div style="font-size: 0.8rem; display: flex; flex-direction: column; gap: 0.25rem;">
                                <div><strong>ID Guru:</strong> <?= esc($guru['id_guru']) ?></div>
                                <div><strong>Mata Pelajaran:</strong> <?= esc($guru['mata_pelajaran']) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Personal Details Form -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                        
                        <!-- NIK -->
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="nik">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" name="nik" id="nik" class="form-control <?= (isset($session_errors['nik'])) ? 'is-invalid' : '' ?>" value="<?= old('nik', $guru['nik']) ?>" placeholder="NIK" required>
                            <?php if (isset($session_errors['nik'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['nik'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Nama Guru -->
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="nama_guru">Nama Lengkap</label>
                            <input type="text" name="nama_guru" id="nama_guru" class="form-control <?= (isset($session_errors['nama_guru'])) ? 'is-invalid' : '' ?>" value="<?= old('nama_guru', $guru['nama_guru']) ?>" placeholder="Nama Lengkap beserta gelar" required>
                            <?php if (isset($session_errors['nama_guru'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['nama_guru'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Jenis Kelamin -->
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

                        <!-- Mata Pelajaran (Read-only) -->
                        <div class="form-group">
                            <label for="mata_pelajaran">Mata Pelajaran Diampu</label>
                            <input type="text" class="form-control" value="<?= esc($guru['mata_pelajaran']) ?>" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
                            <input type="hidden" name="mata_pelajaran" value="<?= esc($guru['mata_pelajaran']) ?>">
                        </div>

                        <!-- Kelas Diajar (Read-only) -->
                        <div class="form-group">
                            <label for="kelas_diajar">Kelas Diajar</label>
                            <input type="text" class="form-control" value="<?= esc($guru['kelas_diajar']) ?>" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
                            <input type="hidden" name="kelas_diajar" value="<?= esc($guru['kelas_diajar']) ?>">
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="form-group">
                            <label for="tempat_lahir">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control <?= (isset($session_errors['tempat_lahir'])) ? 'is-invalid' : '' ?>" value="<?= old('tempat_lahir', $guru['tempat_lahir']) ?>" placeholder="Tempat Lahir">
                            <?php if (isset($session_errors['tempat_lahir'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['tempat_lahir'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control <?= (isset($session_errors['tanggal_lahir'])) ? 'is-invalid' : '' ?>" value="<?= old('tanggal_lahir', $guru['tanggal_lahir']) ?>">
                            <?php if (isset($session_errors['tanggal_lahir'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['tanggal_lahir'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- No HP -->
                        <div class="form-group">
                            <label for="no_hp">No. Handphone</label>
                            <input type="text" name="no_hp" id="no_hp" class="form-control <?= (isset($session_errors['no_hp'])) ? 'is-invalid' : '' ?>" value="<?= old('no_hp', $guru['no_hp']) ?>" placeholder="No HP">
                            <?php if (isset($session_errors['no_hp'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['no_hp'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control <?= (isset($session_errors['email'])) ? 'is-invalid' : '' ?>" value="<?= old('email', $guru['email']) ?>" placeholder="Email">
                            <?php if (isset($session_errors['email'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['email'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Pendidikan Terakhir -->
                        <div class="form-group">
                            <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control <?= (isset($session_errors['pendidikan_terakhir'])) ? 'is-invalid' : '' ?>">
                                <option value="" disabled selected>-- Pilih Pendidikan --</option>
                                <option value="D3" <?= old('pendidikan_terakhir', $guru['pendidikan_terakhir']) === 'D3' ? 'selected' : '' ?>>D3</option>
                                <option value="S1" <?= old('pendidikan_terakhir', $guru['pendidikan_terakhir']) === 'S1' ? 'selected' : '' ?>>S1 / Sarjana</option>
                                <option value="S2" <?= old('pendidikan_terakhir', $guru['pendidikan_terakhir']) === 'S2' ? 'selected' : '' ?>>S2 / Magister</option>
                                <option value="S3" <?= old('pendidikan_terakhir', $guru['pendidikan_terakhir']) === 'S3' ? 'selected' : '' ?>>S3 / Doktor</option>
                                <option value="Lainnya" <?= old('pendidikan_terakhir', $guru['pendidikan_terakhir']) === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                            </select>
                            <?php if (isset($session_errors['pendidikan_terakhir'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['pendidikan_terakhir'] ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Status Kepegawaian (Read-only) -->
                        <div class="form-group">
                            <label for="status_kepegawaian">Status Kepegawaian</label>
                            <input type="text" class="form-control" value="<?= esc($guru['status_kepegawaian']) ?>" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
                            <input type="hidden" name="status_kepegawaian" value="<?= esc($guru['status_kepegawaian']) ?>">
                        </div>

                        <!-- Tanggal Masuk (Read-only) -->
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="tanggal_masuk">Tanggal Masuk</label>
                            <input type="text" class="form-control" value="<?= !empty($guru['tanggal_masuk']) ? date('d-m-Y', strtotime($guru['tanggal_masuk'])) : 'Belum diisi' ?>" disabled style="background-color: #e2e8f0; cursor: not-allowed; color: var(--text-muted);">
                            <input type="hidden" name="tanggal_masuk" value="<?= esc($guru['tanggal_masuk']) ?>">
                        </div>

                        <!-- Alamat -->
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="alamat">Alamat Lengkap</label>
                            <textarea name="alamat" id="alamat" class="form-control <?= (isset($session_errors['alamat'])) ? 'is-invalid' : '' ?>" rows="3" placeholder="Alamat lengkap tempat tinggal"><?= old('alamat', $guru['alamat']) ?></textarea>
                            <?php if (isset($session_errors['alamat'])): ?>
                                <div class="invalid-feedback"><?= $session_errors['alamat'] ?></div>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2.5rem; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 1.5rem;">
                    <a href="<?= base_url('/profil/guru') ?>" class="btn btn-secondary" style="padding: 0.75rem 1.5rem;">Batal</a>
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
                        <?php if (!empty($guru['foto']) && file_exists(ROOTPATH . 'public/uploads/foto/' . $guru['foto'])): ?>
                            <img src="<?= base_url('uploads/foto/' . $guru['foto']) ?>" alt="Foto Profil" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fa-solid fa-user-tie" style="font-size: 5rem; color: #a0aec0;"></i>
                        <?php endif; ?>
                    </div>

                    <div style="width: 100%; text-align: left; background: rgba(99, 102, 241, 0.03); border-radius: var(--border-radius-md); padding: 1.25rem; border: 1px solid rgba(99, 102, 241, 0.15);">
                        <h4 style="margin: 0 0 0.75rem 0; font-size: 0.95rem; color: var(--primary); font-family: var(--font-heading); font-weight: 700; border-bottom: 1px solid rgba(99,102,241,0.15); padding-bottom: 0.5rem;">Informasi Kepegawaian</h4>
                        <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            <div><strong style="color: var(--text-muted);">ID Guru:</strong> <span style="font-weight: 600; color: var(--text-main);"><?= esc($guru['id_guru']) ?></span></div>
                            <div><strong style="color: var(--text-muted);">Mata Pelajaran:</strong> <span style="font-weight: 600; color: var(--text-main);"><?= esc($guru['mata_pelajaran']) ?></span></div>
                            <div><strong style="color: var(--text-muted);">Kelas Diajar:</strong> <span style="font-weight: 600; color: var(--text-main);"><?= esc($guru['kelas_diajar']) ?></span></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Personal & Kepegawaian details -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <!-- Box Data Pribadi -->
                    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--border-radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                        <h4 style="margin: 0 0 1.25rem 0; font-size: 1.05rem; color: var(--primary-dark); font-family: var(--font-heading); font-weight: 700; border-bottom: 1.5px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-user" style="font-size: 0.95rem;"></i>
                            <span>Data Pribadi Guru</span>
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; font-size: 0.925rem;">
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">NIK</span>
                                <span style="font-weight: 700; color: var(--text-main);"><?= esc($guru['nik']) ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Nama Lengkap</span>
                                <span style="font-weight: 700; color: var(--text-main); font-size: 1rem;"><?= esc($guru['nama_guru']) ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Jenis Kelamin</span>
                                <span style="font-weight: 600; color: var(--text-main);"><?= $guru['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki' ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Tempat, Tanggal Lahir</span>
                                <span style="font-weight: 600; color: var(--text-main);">
                                    <?= (!empty($guru['tempat_lahir']) || !empty($guru['tanggal_lahir'])) ? esc($guru['tempat_lahir']) . ', ' . (!empty($guru['tanggal_lahir']) ? date('d-m-Y', strtotime($guru['tanggal_lahir'])) : '') : '<em style="color: var(--text-light);">Belum diisi</em>' ?>
                                </span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">No. Handphone</span>
                                <span style="font-weight: 600; color: var(--text-main); font-family: monospace;"><?= !empty($guru['no_hp']) ? esc($guru['no_hp']) : '<em style="color: var(--text-light);">Belum diisi</em>' ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Email</span>
                                <span style="font-weight: 600; color: var(--text-main);"><?= !empty($guru['email']) ? esc($guru['email']) : '<em style="color: var(--text-light);">Belum diisi</em>' ?></span>
                            </div>
                            <div style="grid-column: span 2;">
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Alamat Rumah</span>
                                <span style="font-weight: 500; color: var(--text-main); line-height: 1.4; display: block;">
                                    <?= !empty($guru['alamat']) ? nl2br(esc($guru['alamat'])) : '<em style="color: var(--text-light);">Belum diisi</em>' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Box Detail Kepegawaian & Pendidikan -->
                    <div style="background: #ffffff; border: 1px solid var(--border-color); border-radius: var(--border-radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                        <h4 style="margin: 0 0 1.25rem 0; font-size: 1.05rem; color: var(--secondary); font-family: var(--font-heading); font-weight: 700; border-bottom: 1.5px solid var(--border-color); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-graduation-cap" style="font-size: 0.95rem;"></i>
                            <span>Pendidikan & Kepegawaian</span>
                        </h4>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; font-size: 0.925rem;">
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Pendidikan Terakhir</span>
                                <span style="font-weight: 600; color: var(--text-main);"><?= !empty($guru['pendidikan_terakhir']) ? esc($guru['pendidikan_terakhir']) : '<em style="color: var(--text-light);">Belum diisi</em>' ?></span>
                            </div>
                            <div>
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Status Kepegawaian</span>
                                <span style="font-weight: 600; color: var(--text-main);"><?= !empty($guru['status_kepegawaian']) ? esc($guru['status_kepegawaian']) : '<em style="color: var(--text-light);">Belum diisi</em>' ?></span>
                            </div>
                            <div style="grid-column: span 2;">
                                <span style="display: block; color: var(--text-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Tanggal Masuk Kerja</span>
                                <span style="font-weight: 600; color: var(--text-main);">
                                    <?= !empty($guru['tanggal_masuk']) ? date('d-m-Y', strtotime($guru['tanggal_masuk'])) : '<em style="color: var(--text-light);">Belum diisi</em>' ?>
                                </span>
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
