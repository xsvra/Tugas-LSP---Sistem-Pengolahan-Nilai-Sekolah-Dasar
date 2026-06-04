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
            padding: 1.5rem;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius-lg);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            color: #ffffff;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        .login-logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: var(--border-radius-md);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.5rem;
            color: #ffffff;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }
        .login-header h2 {
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }
        .login-header p {
            color: var(--text-light);
            font-size: 0.85rem;
        }
        .login-card label {
            color: #e2e8f0;
            font-weight: 500;
        }
        .login-card .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
        .login-card .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }
        .login-card .form-control::placeholder {
            color: #94a3b8;
        }
        .register-link {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #cbd5e1;
        }
        .register-link a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .alert {
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">SN</div>
            <h2>Sistem Nilai</h2>
            <p>Silakan masuk menggunakan akun Anda</p>
        </div>

        <!-- Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-xmark"></i>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login/process') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required value="<?= old('username') ?>">
            </div>

            <div class="form-group" style="margin-top: 1.25rem;">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 2rem; padding: 0.75rem;">
                <span>Masuk Sekarang</span>
                <i class="fa-solid fa-right-to-bracket"></i>
            </button>
        </form>

        <div class="register-link">
            Belum memiliki akun? <a href="<?= base_url('register') ?>">Daftar Di Sini</a>
        </div>
    </div>
</body>
</html>
