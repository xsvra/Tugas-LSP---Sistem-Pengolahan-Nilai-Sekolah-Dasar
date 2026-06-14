<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Sistem Pengolahan Nilai</title>
    <!-- FontAwesome Icon CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar no-print">
            <div class="sidebar-sticky-wrapper">
                <div class="sidebar-brand">
                    <div class="sidebar-logo">SN</div>
                    <span class="sidebar-title">Sistem Nilai</span>
                </div>
                <ul class="sidebar-menu">
                    <?php if (session()->get('role') === 'admin'): ?>
                        <li>
                            <a href="<?= base_url('/') ?>" class="sidebar-link <?= current_url() == base_url('/') ? 'active' : '' ?>">
                                <i class="fa-solid fa-chart-pie"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <!-- Admin Menus -->
                        <li>
                            <a href="<?= base_url('/siswa') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/siswa')) !== false ? 'active' : '' ?>">
                                <i class="fa-solid fa-graduation-cap"></i>
                                <span>Data Siswa</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('/guru') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/guru')) !== false ? 'active' : '' ?>">
                                <i class="fa-solid fa-chalkboard-user"></i>
                                <span>Data Guru</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (session()->get('role') === 'guru'): ?>
                        <!-- Guru Menus -->
                        <li>
                            <a href="<?= base_url('/profil/guru') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/profil/guru')) !== false ? 'active' : '' ?>">
                                <i class="fa-solid fa-address-card"></i>
                                <span>Data Diri</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('/nilai') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/nilai')) !== false ? 'active' : '' ?>">
                                <i class="fa-solid fa-file-invoice"></i>
                                <span>Input Nilai</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('/laporan') ?>" class="sidebar-link <?= current_url() == base_url('/laporan') ? 'active' : '' ?>">
                                <i class="fa-solid fa-file-contract"></i>
                                <span>Laporan Nilai</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('/banding/tinjau') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/banding/tinjau')) !== false ? 'active' : '' ?>">
                                <i class="fa-solid fa-comments-dollar" style="transform: scaleX(-1);"></i>
                                <span>Tinjau Banding</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (session()->get('role') === 'siswa'): ?>
                        <!-- Siswa Menus -->
                        <li>
                            <a href="<?= base_url('/profil/siswa') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/profil/siswa')) !== false ? 'active' : '' ?>">
                                <i class="fa-solid fa-address-card"></i>
                                <span>Data Diri</span>
                            </a>
                        </li>
                        <?php if (session()->get('ref_id')): ?>
                            <li>
                                <a href="<?= base_url('/laporan/rapor/' . session()->get('ref_id')) ?>" class="sidebar-link <?= strpos(current_url(), base_url('/laporan/rapor')) !== false ? 'active' : '' ?>">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    <span>Lihat Rapor</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?= base_url('/banding/riwayat') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/banding/riwayat')) !== false ? 'active' : '' ?>">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Riwayat Banding</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li>
                        <a href="<?= base_url('/dokumen-rancangan') ?>" class="sidebar-link <?= strpos(current_url(), base_url('/dokumen-rancangan')) !== false ? 'active' : '' ?>">
                            <i class="fa-solid fa-book-bookmark"></i>
                            <span>Dokumen Tugas 1</span>
                        </a>
                    </li>
                    
                    <li style="margin-top: auto;">
                        <a href="<?= base_url('/logout') ?>" class="sidebar-link" onclick="return confirm('Apakah Anda yakin ingin keluar?');" style="color: var(--danger-light); background: rgba(239, 68, 68, 0.05);">
                            <i class="fa-solid fa-right-from-bracket" style="color: var(--danger);"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Header Navbar -->
            <header class="header-nav no-print">
                <div class="page-info">
                    <h1><?= isset($title) ? $title : 'Selamat Datang' ?></h1>
                    <p>Sistem Pengolahan Nilai Siswa (MVC + OOP + Terstruktur)</p>
                </div>
                <div class="user-profile">
                    <div class="user-avatar" style="background-color: var(--secondary); color: white; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px solid #ffffff; box-shadow: var(--shadow-sm); width: 40px; height: 40px; border-radius: 50%;">
                        <?php if (session()->get('foto') && file_exists(ROOTPATH . 'public/uploads/foto/' . session()->get('foto'))): ?>
                            <img src="<?= base_url('uploads/foto/' . session()->get('foto')) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?= strtoupper(substr(session()->get('username'), 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="user-details" style="display: flex; flex-direction: column;">
                        <?php 
                        $displayName = session()->get('username');
                        if (session()->get('role') === 'guru' && session()->get('nama_lengkap')) {
                            $displayName = session()->get('nama_lengkap');
                        }
                        ?>
                        <span style="font-weight: 600; font-size: 0.9rem;"><?= esc($displayName) ?></span>
                        <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;"><?= esc(session()->get('role')) ?></span>
                    </div>
                </div>
            </header>

            <!-- Dynamic Session Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success no-print">
                    <i class="fa-solid fa-circle-check"></i>
                    <span><?= session()->getFlashdata('success') ?></span>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger no-print">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span><?= session()->getFlashdata('error') ?></span>
                </div>
            <?php endif; ?>

            <!-- Render Section Content -->
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</body>
</html>
