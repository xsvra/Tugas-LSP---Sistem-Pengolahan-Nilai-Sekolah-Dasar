<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public / Authentication Routes
$routes->get('login', 'AuthController::login');
$routes->post('login/process', 'AuthController::loginProcess');
$routes->get('register', 'AuthController::register');
$routes->post('register/process', 'AuthController::registerProcess');
$routes->get('logout', 'AuthController::logout');

// Authenticated General Routes
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dokumen-rancangan', 'Dashboard::dokumenRancangan');
});

// Admin-Only Routes: Manage Siswa and Guru CRUD
$routes->group('', ['filter' => 'auth:admin'], function($routes) {
    // Siswa CRUD
    $routes->group('siswa', function($routes) {
        $routes->get('/', 'SiswaController::index');
        $routes->get('create', 'SiswaController::create');
        $routes->get('get-next-nis', 'SiswaController::getNextNis');
        $routes->post('store', 'SiswaController::store');
        $routes->get('edit/(:any)', 'SiswaController::edit/$1');
        $routes->post('update/(:any)', 'SiswaController::update/$1');
        $routes->get('delete/(:any)', 'SiswaController::delete/$1');
    });

    // Guru CRUD
    $routes->group('guru', function($routes) {
        $routes->get('/', 'GuruController::index');
        $routes->get('create', 'GuruController::create');
        $routes->post('store', 'GuruController::store');
        $routes->get('edit/(:any)', 'GuruController::edit/$1');
        $routes->post('update/(:any)', 'GuruController::update/$1');
        $routes->get('delete/(:any)', 'GuruController::delete/$1');
    });
});

// Guru-Only Routes: Input grades & review appeals
$routes->group('', ['filter' => 'auth:guru'], function($routes) {
    // Profil Guru
    $routes->get('profil/guru', 'GuruController::profil');
    $routes->post('profil/guru/update', 'GuruController::profilUpdate');

    // Nilai CRUD
    $routes->group('nilai', function($routes) {
        $routes->get('/', 'NilaiController::index');
        $routes->get('create', 'NilaiController::create');
        $routes->get('siswa-by-kelas/(:num)', 'NilaiController::getSiswaByKelas/$1');
        $routes->post('store', 'NilaiController::store');
        $routes->get('edit/(:num)', 'NilaiController::edit/$1');
        $routes->post('update/(:num)', 'NilaiController::update/$1');
        $routes->get('delete/(:num)', 'NilaiController::delete/$1');
    });

    // Laporan Rekap
    $routes->get('laporan', 'LaporanController::index');

    // Appeals review
    $routes->group('banding', function($routes) {
        $routes->get('tinjau', 'BandingController::tinjau');
        $routes->post('tinjau/update/(:num)', 'BandingController::tinjauUpdate/$1');
    });
});

// Siswa-Only Routes: Profil, Appeal submission
$routes->group('', ['filter' => 'auth:siswa'], function($routes) {
    $routes->get('profil/siswa', 'SiswaController::profil');
    $routes->post('profil/siswa/update', 'SiswaController::profilUpdate');
    
    $routes->group('banding', function($routes) {
        $routes->get('ajukan/(:num)', 'BandingController::ajukan/$1');
        $routes->post('ajukan/process/(:num)', 'BandingController::ajukanProcess/$1');
        $routes->get('riwayat', 'BandingController::riwayat');
    });
});

// Shared routes (Guru & Siswa can both view Rapor)
$routes->group('', ['filter' => 'auth:guru,siswa'], function($routes) {
    $routes->get('laporan/rapor/(:any)', 'LaporanController::rapor/$1');
});
