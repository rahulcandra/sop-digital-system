<?php
session_start();
require_once 'config/database.php';

$message = '';
$msg_type = '';

$base_url = "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER["REQUEST_URI"] . '?');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    // ✅ FIX #1 — Cari berdasarkan kolom email, bukan username
    $sql = "SELECT id, nama_lengkap, email FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $token = bin2hex(random_bytes(50));

        date_default_timezone_set('Asia/Jakarta');
        $expire_time = date("Y-m-d H:i:s", strtotime('+1 hours'));

        // ✅ FIX #2 — Update berdasarkan kolom email, bukan username
        $update_sql = "UPDATE users SET reset_token = '$token', token_expire = '$expire_time' WHERE email = '$email'";
        mysqli_query($conn, $update_sql);

        $reset_link = $base_url . "/reset_password.php?token=" . $token;

        // ✅ FIX #3 — Gunakan email dari database, bukan dari input form
        $to = $user['email'];
        $subject = "Permintaan Reset Password - SOP Digital";

        $email_body  = "Halo " . $user['nama_lengkap'] . ",\n\n";
        $email_body .= "Kami menerima permintaan untuk mereset password akun SOP Digital Anda.\n";
        $email_body .= "Silakan klik tautan di bawah ini untuk membuat password baru:\n\n";
        $email_body .= $reset_link . "\n\n";
        $email_body .= "Tautan ini hanya berlaku selama 1 jam.\n";
        $email_body .= "Jika Anda tidak meminta reset password, abaikan email ini.\n\n";
        $email_body .= "Terima kasih,\nTim IT Sinergi Nusantara Integrasi";

        $headers  = "From: no-reply@sinergi.co.id\r\n";
        $headers .= "Reply-To: it@sinergi.co.id\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if (@mail($to, $subject, $email_body, $headers)) {
            $msg_type = 'success';
            $message  = 'Tautan reset password telah dikirim ke email Anda. Silakan cek Inbox atau folder Spam.';
        } else {
            $msg_type = 'warning';
            $message  = 'Gagal mengirim email. Sistem sedang berjalan di Localhost.';
            $message .= '<br><br><b>[MODE TESTING LOCALHOST]</b> Link Anda: <a href="' . $reset_link . '" style="color:inherit;text-decoration:underline;">Klik Disini</a>';
        }
    } else {
        // Pesan generik agar tidak bocorkan info akun
        $msg_type = 'info';
        $message  = 'Jika email tersebut terdaftar, tautan reset telah dikirimkan. Silakan cek Inbox Anda.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SOP Digital System</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-glow: #3b82f6;
            --secondary-glow: #8b5cf6;
            --accent-orange: #f97316;
            --bg-main: #020617;
            --glass-bg: rgba(15, 23, 42, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: rgba(0, 0, 0, 0.4);
            --grid-color: rgba(255, 255, 255, 0.03);
        }

        [data-theme="light"] {
            --bg-main: #f1f5f9;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(0, 0, 0, 0.1);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --input-bg: rgba(255, 255, 255, 0.9);
            --grid-color: rgba(0, 0, 0, 0.04);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: var(--bg-main) !important;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            overflow-x: hidden;
            transition: background-color 0.5s ease, color 0.5s ease;
        }

        /* --- Background --- */
        .ambient-light {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background:
                radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(249, 115, 22, 0.08), transparent 25%);
        }

        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(80px); opacity: 0.6;
            animation: moveOrb 20s infinite alternate;
        }
        .orb-1 { width: 400px; height: 400px; background: var(--primary-glow); top: -100px; left: -100px; }
        .orb-2 { width: 500px; height: 500px; background: var(--accent-orange); bottom: -150px; right: -150px; animation-delay: -5s; }

        @keyframes moveOrb {
            0%   { transform: translate(0, 0); }
            100% { transform: translate(50px, 30px); }
        }

        .grid-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-image:
                linear-gradient(var(--grid-color) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-color) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: -1;
            mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
            -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 100%);
        }

        /* --- Theme Toggle --- */
        .theme-toggle {
            position: fixed; top: 20px; right: 20px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            width: 45px; height: 45px;
            border-radius: 50%; cursor: pointer;
            display: flex; justify-content: center; align-items: center;
            font-size: 18px; z-index: 100;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .theme-toggle:hover { transform: scale(1.1); color: var(--primary-glow); }

        /* --- Card --- */
        .login-container {
            width: 100%; padding: 20px;
            display: flex; justify-content: center; align-items: center;
            z-index: 10;
        }

        .login-box {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(25px) !important;
            -webkit-backdrop-filter: blur(25px) !important;
            border: 1px solid var(--glass-border) !important;
            border-radius: 24px !important;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3), 0 0 40px rgba(59,130,246,0.08) !important;
            max-width: 460px !important;
            width: 100% !important;
            overflow: hidden;
            animation: cardEntrance 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
            transition: background 0.5s ease, border-color 0.5s ease;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .login-right {
            padding: 45px 40px !important;
            display: flex; flex-direction: column; justify-content: center;
        }

        /* --- Icon animasi --- */
        .icon-wrap {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(139,92,246,0.15));
            border: 1px solid rgba(59,130,246,0.3);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59,130,246,0.3); }
            50%       { box-shadow: 0 0 0 10px rgba(59,130,246,0); }
        }

        /* --- Form Header --- */
        .form-header { text-align: center; margin-bottom: 28px; }
        .form-header h2 {
            color: var(--text-main) !important;
            font-size: 24px !important;
            font-weight: 700;
            margin: 0 0 8px !important;
            letter-spacing: -0.5px;
        }
        .form-header p {
            color: var(--text-muted) !important;
            font-size: 13px !important;
            line-height: 1.6;
            margin: 0 !important;
        }

        /* --- Form Group --- */
        .form-group { margin-bottom: 22px !important; position: relative; }
        .form-group label {
            color: var(--text-main) !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px !important;
            display: block; margin-left: 4px;
        }

        .input-group { position: relative; width: 100%; }
        .input-group i {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted); font-size: 15px;
            transition: 0.3s; z-index: 2;
        }

        .form-control {
            width: 100%;
            padding: 14px 14px 14px 45px !important;
            background: var(--input-bg) !important;
            border: 1px solid var(--glass-border) !important;
            border-radius: 12px !important;
            color: var(--text-main) !important;
            font-size: 14px !important;
            font-family: 'Outfit', sans-serif !important;
            transition: all 0.3s ease !important;
            box-sizing: border-box;
        }
        .form-control::placeholder { color: var(--text-muted); font-weight: 300; }
        .form-control:focus {
            outline: none !important;
            border-color: var(--primary-glow) !important;
            background: var(--glass-bg) !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1) !important;
        }
        .form-control:focus + i { color: var(--primary-glow); transform: translateY(-50%) scale(1.1); }

        /* --- Button --- */
        .btn-primary {
            width: 100% !important;
            padding: 14px !important;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6) !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            font-family: 'Outfit', sans-serif !important;
            cursor: pointer !important;
            letter-spacing: 0.5px;
            transition: all 0.3s ease !important; /* ✅ FIX #4 — tambah transition */
            box-shadow: 0 4px 15px rgba(59,130,246,0.3) !important;
            margin-top: 5px;
        }
        .btn-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(139,92,246,0.4) !important;
        }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed !important;
            transform: none !important;
        }

        /* --- Alert --- */
        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 22px !important;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            backdrop-filter: blur(10px);
            animation: slideDown 0.4s ease;
            line-height: 1.5;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert i { flex-shrink: 0; margin-top: 1px; }

        .alert-danger  { background: rgba(239,68,68,0.1) !important;   color: #ef4444 !important; border: 1px solid rgba(239,68,68,0.2) !important; }
        .alert-success { background: rgba(16,185,129,0.1) !important;  color: #10b981 !important; border: 1px solid rgba(16,185,129,0.2) !important; }
        .alert-warning { background: rgba(249,115,22,0.1) !important;  color: #f97316 !important; border: 1px solid rgba(249,115,22,0.2) !important; }
        .alert-info    { background: rgba(59,130,246,0.1) !important;  color: #60a5fa !important; border: 1px solid rgba(59,130,246,0.2) !important; }

        .alert-warning a, .alert-info a { color: inherit; }

        /* --- Kembali ke Login --- */
        .back-link {
            text-align: center;
            margin-top: 24px;
        }
        .back-link a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .back-link a:hover { color: var(--primary-glow); }

        /* --- Divider & Footer --- */
        .divider {
            border: none;
            border-top: 1px solid var(--glass-border);
            margin: 22px 0 16px;
        }
        .demo-info {
            text-align: center;
            color: var(--text-muted);
            font-size: 11px;
        }

        /* Mobile */
        @media (max-width: 500px) {
            .login-right { padding: 30px 22px !important; }
        }
    </style>
</head>
<body>

    <!-- Toggle Tema -->
    <button class="theme-toggle" id="theme-toggle" title="Ubah Tema">
        <i class="fas fa-moon"></i>
    </button>

    <div class="grid-overlay"></div>
    <div class="ambient-light">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <div class="login-container">
        <div class="login-box">
            <div class="login-right">

                <!-- Icon -->
                <div class="icon-wrap">
                    <i class="fas fa-unlock-alt" style="font-size: 28px; color: var(--primary-glow);"></i>
                </div>

                <!-- Header -->
                <div class="form-header">
                    <h2>Lupa Password?</h2>
                    <p>Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang password.</p>
                </div>

                <!-- Alert pesan -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $msg_type; ?>">
                        <i class="fas <?php
                            echo $msg_type == 'success' ? 'fa-check-circle'
                                : ($msg_type == 'warning' ? 'fa-exclamation-triangle'
                                : ($msg_type == 'info'    ? 'fa-info-circle'
                                : 'fa-times-circle'));
                        ?>"></i>
                        <div><?php echo $message; ?></div>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" action="" id="forgotForm">
                    <div class="form-group">
                        <label for="email">Alamat Email Terdaftar</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control"
                                   placeholder="contoh: karyawan@sinergi.co.id"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   required autocomplete="email">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Kirim Link Reset
                    </button>
                </form>

                <!-- Kembali ke login -->
                <div class="back-link">
                    <a href="index.php">
                        <i class="fas fa-arrow-left"></i> Kembali ke Login
                    </a>
                </div>

                <hr class="divider">
                <div class="demo-info">
                    <p style="margin:0;">&copy; <?php echo date('Y'); ?> PT. Sinergi Nusantara Integrasi</p>
                    <p style="margin:5px 0 0; font-size:10px; color:#aaa; letter-spacing:0.5px;">
                        Developed by <span style="color:#666;">Rahul Candra</span>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- Sinkron tema dengan halaman lain ---
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon      = themeToggleBtn.querySelector('i');
        const htmlEl         = document.documentElement;

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            htmlEl.setAttribute('data-theme', 'light');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        }

        themeToggleBtn.addEventListener('click', function () {
            if (htmlEl.getAttribute('data-theme') === 'light') {
                htmlEl.removeAttribute('data-theme');
                themeIcon.classList.replace('fa-sun', 'fa-moon');
                localStorage.setItem('theme', 'dark');
            } else {
                htmlEl.setAttribute('data-theme', 'light');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
                localStorage.setItem('theme', 'light');
            }
        });

        // --- Disable tombol saat submit agar tidak double-submit ---
        const form      = document.getElementById('forgotForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Mengirim...';
        });

    });
    </script>
</body>
</html>