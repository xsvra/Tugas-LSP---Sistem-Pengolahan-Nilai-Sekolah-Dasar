<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            padding: 2rem 1.5rem;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius-lg);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            color: #ffffff;
        }
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .register-logo {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--border-radius-md);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.3rem;
            color: #ffffff;
            margin-bottom: 0.75rem;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }
        .register-header h2 {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }
        .register-header p {
            color: var(--text-light);
            font-size: 0.85rem;
        }
        .register-card label {
            color: #e2e8f0;
            font-weight: 500;
        }
        .register-card .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .register-card select.form-control option {
            background-color: var(--bg-sidebar);
            color: #ffffff;
        }
        .register-card .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }
        .register-card .form-control::placeholder {
            color: #94a3b8;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #cbd5e1;
        }
        .login-link a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .invalid-feedback {
            color: #fca5a5;
        }
    </style>
    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const siswaFields = document.getElementById('siswa-fields');
            const guruFields = document.getElementById('guru-fields');
            const refIdLabel = document.getElementById('ref-id-label');
            const refIdInput = document.getElementById('ref_id');

            if (role === 'siswa') {
                siswaFields.style.display = 'block';
                guruFields.style.display = 'none';
                refIdLabel.innerText = 'Nomor Induk Siswa (NIS)';
                refIdInput.placeholder = 'Contoh: 102';
                document.getElementById('kelas').required = true;
                document.getElementById('nik').required = false;
                document.getElementById('mata_pelajaran').required = false;
            } else if (role === 'guru') {
                siswaFields.style.display = 'none';
                guruFields.style.display = 'block';
                refIdLabel.innerText = 'ID Guru / NIP';
                refIdInput.placeholder = 'Contoh: G02';
                document.getElementById('kelas').required = false;
                document.getElementById('nik').required = true;
                document.getElementById('mata_pelajaran').required = true;
            }
        }
    </script>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <div class="register-logo">SN</div>
            <h2>Daftar Akun Baru</h2>
            <p>Silakan isi formulir di bawah ini untuk mendaftar sebagai Siswa</p>
        </div>

        <?php $session_errors = session()->getFlashdata('errors'); ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem; font-size: 0.85rem; padding: 0.75rem 1rem;">
                <i class="fa-solid fa-circle-xmark"></i>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('register/process') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control <?= (isset($session_errors['username'])) ? 'is-invalid' : '' ?>" placeholder="Masukkan username" required value="<?= old('username') ?>">
                <?php if (isset($session_errors['username'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['username'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Password -->
            <div class="form-group" style="margin-top: 1.25rem;">
                <label for="password">Password (min. 6 karakter)</label>
                <input type="password" name="password" id="password" class="form-control <?= (isset($session_errors['password'])) ? 'is-invalid' : '' ?>" placeholder="Masukkan password" required>
                <?php if (isset($session_errors['password'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['password'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Konfirmasi Password -->
            <div class="form-group" style="margin-top: 1.25rem;">
                <label for="konfirmasi_password">Konfirmasi Password</label>
                <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control <?= (isset($session_errors['konfirmasi_password'])) ? 'is-invalid' : '' ?>" placeholder="Masukkan kembali password" required>
                <?php if (isset($session_errors['konfirmasi_password'])): ?>
                    <div class="invalid-feedback"><?= $session_errors['konfirmasi_password'] ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 2rem; padding: 0.75rem;">
                <span>Daftar Akun</span>
                <i class="fa-solid fa-user-plus"></i>
            </button>
        </form>

        <div class="login-link">
            Sudah memiliki akun? <a href="<?= base_url('login') ?>">Login Di Sini</a>
        </div>
    </div>
</body>
</html>
