<?php
session_start();
require_once 'config/database.php';

$message  = '';
$msg_type = '';
$token_valid = false;
$token = '';

// --- Ambil & validasi token dari URL ---
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    date_default_timezone_set('Asia/Jakarta');
    $now = date("Y-m-d H:i:s");

    $sql    = "SELECT id, nama_lengkap FROM users WHERE reset_token = '$token' AND token_expire > '$now'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $token_valid = true;
    } else {
        $msg_type = 'danger';
        $message  = 'Tautan reset password tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.';
    }
} else {
    $msg_type = 'danger';
    $message  = 'Token tidak ditemukan. Silakan minta tautan reset password kembali.';
}

// --- Proses form ganti password ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token_valid) {
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $msg_type = 'danger';
        $message  = 'Semua field wajib diisi!';
    } elseif (strlen($new_password) < 6) {
        $msg_type = 'danger';
        $message  = 'Password minimal 6 karakter!';
    } elseif ($new_password !== $confirm_password) {
        $msg_type = 'danger';
        $message  = 'Password dan konfirmasi password tidak cocok!';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password & hapus token
        $update = "UPDATE users SET password = '$hashed', reset_token = NULL, token_expire = NULL WHERE reset_token = '$token'";

        if (mysqli_query($conn, $update)) {
            // Redirect ke login dengan pesan sukses
            header('Location: index.php?reset=1');
            exit();
        } else {
            $msg_type = 'danger';
            $message  = 'Terjadi kesalahan saat menyimpan password baru. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SOP Digital System</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-glow:   #3b82f6;
            --secondary-glow: #8b5cf6;
            --accent-orange:  #f97316;
            --bg-main:        #020617;
            --glass-bg:       rgba(15, 23, 42, 0.7);
            --glass-border:   rgba(255, 255, 255, 0.1);
            --text-main:      #f8fafc;
            --text-muted:     #94a3b8;
            --input-bg:       rgba(0, 0, 0, 0.4);
            --grid-color:     rgba(255, 255, 255, 0.03);
        }
        [data-theme="light"] {
            --bg-main:      #f1f5f9;
            --glass-bg:     rgba(255, 255, 255, 0.85);
            --glass-border: rgba(0, 0, 0, 0.1);
            --text-main:    #0f172a;
            --text-muted:   #64748b;
            --input-bg:     rgba(255, 255, 255, 0.9);
            --grid-color:   rgba(0, 0, 0, 0.04);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: var(--bg-main) !important;
            color: var(--text-main);
            min-height: 100vh;
            display: flex; justify-content: center; align-items: center;
            margin: 0; overflow-x: hidden;
            transition: background-color 0.5s ease, color 0.5s ease;
        }

        /* Background */
        .ambient-light {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background:
                radial-gradient(circle at 15% 50%, rgba(59,130,246,0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(249,115,22,0.08),  transparent 25%);
        }
        .orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.6; animation: moveOrb 20s infinite alternate; }
        .orb-1 { width:400px; height:400px; background:var(--primary-glow);  top:-100px;    left:-100px; }
        .orb-2 { width:500px; height:500px; background:var(--accent-orange); bottom:-150px; right:-150px; animation-delay:-5s; }
        @keyframes moveOrb { 0%{transform:translate(0,0)} 100%{transform:translate(50px,30px)} }

        .grid-overlay {
            position: fixed; top:0; left:0; width:100%; height:100%;
            background-image: linear-gradient(var(--grid-color) 1px,transparent 1px), linear-gradient(90deg,var(--grid-color) 1px,transparent 1px);
            background-size: 40px 40px; z-index:-1;
            mask-image: radial-gradient(circle at center,black 40%,transparent 100%);
            -webkit-mask-image: radial-gradient(circle at center,black 40%,transparent 100%);
        }

        /* Theme toggle */
        .theme-toggle {
            position:fixed; top:20px; right:20px;
            background:var(--glass-bg); border:1px solid var(--glass-border);
            color:var(--text-main); width:45px; height:45px; border-radius:50%;
            cursor:pointer; display:flex; justify-content:center; align-items:center;
            font-size:18px; z-index:100; transition:all 0.3s ease;
            backdrop-filter:blur(10px); box-shadow:0 4px 6px rgba(0,0,0,0.1);
        }
        .theme-toggle:hover { transform:scale(1.1); color:var(--primary-glow); }

        /* Card */
        .login-container {
            width:100%; padding:20px;
            display:flex; justify-content:center; align-items:center; z-index:10;
        }
        .login-box {
            background:var(--glass-bg) !important;
            backdrop-filter:blur(25px) !important; -webkit-backdrop-filter:blur(25px) !important;
            border:1px solid var(--glass-border) !important;
            border-radius:24px !important;
            box-shadow:0 25px 50px -12px rgba(0,0,0,0.3), 0 0 40px rgba(59,130,246,0.08) !important;
            max-width:460px !important; width:100% !important; overflow:hidden;
            animation:cardEntrance 0.6s cubic-bezier(0.2,0.8,0.2,1);
            transition:background 0.5s ease, border-color 0.5s ease;
        }
        @keyframes cardEntrance {
            from{opacity:0;transform:scale(0.95) translateY(20px)}
            to  {opacity:1;transform:scale(1) translateY(0)}
        }
        .card-body { padding:45px 40px !important; }

        /* Icon */
        .icon-wrap {
            width:72px; height:72px;
            background:linear-gradient(135deg,rgba(59,130,246,0.15),rgba(139,92,246,0.15));
            border:1px solid rgba(59,130,246,0.3); border-radius:20px;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 20px; animation:iconPulse 3s ease-in-out infinite;
        }
        @keyframes iconPulse {
            0%,100%{box-shadow:0 0 0 0   rgba(59,130,246,0.3)}
            50%    {box-shadow:0 0 0 10px rgba(59,130,246,0)}
        }

        /* Header */
        .form-header { text-align:center; margin-bottom:28px; }
        .form-header h2 {
            color:var(--text-main) !important; font-size:24px !important;
            font-weight:700; margin:0 0 8px !important; letter-spacing:-0.5px;
        }
        .form-header p {
            color:var(--text-muted) !important; font-size:13px !important;
            line-height:1.6; margin:0 !important;
        }

        /* Form */
        .form-group { margin-bottom:20px !important; position:relative; }
        .form-group label {
            color:var(--text-main) !important; font-size:11px !important;
            font-weight:600 !important; text-transform:uppercase; letter-spacing:1px;
            margin-bottom:8px !important; display:block; margin-left:4px;
        }
        .input-group { position:relative; width:100%; }
        .input-group i {
            position:absolute; left:16px; top:50%; transform:translateY(-50%);
            color:var(--text-muted); font-size:15px; transition:0.3s; z-index:2;
        }
        .input-group i.toggle-password {
            left:auto !important; right:16px; cursor:pointer; z-index:10;
        }
        .input-group i.toggle-password:hover { color:var(--primary-glow); }

        .form-control {
            width:100%; padding:13px 45px !important;
            background:var(--input-bg) !important; border:1px solid var(--glass-border) !important;
            border-radius:12px !important; color:var(--text-main) !important;
            font-size:14px !important; font-family:'Outfit',sans-serif !important;
            transition:all 0.3s ease !important;
        }
        .form-control::placeholder { color:var(--text-muted); font-weight:300; }
        .form-control:focus {
            outline:none !important; border-color:var(--primary-glow) !important;
            background:var(--glass-bg) !important; box-shadow:0 0 0 3px rgba(59,130,246,0.1) !important;
        }

        /* Password strength */
        .strength-bar-wrap { margin-top:8px; height:4px; background:var(--glass-border); border-radius:10px; overflow:hidden; display:none; }
        .strength-bar { height:100%; border-radius:10px; width:0%; transition:width 0.4s ease,background 0.4s ease; }
        .strength-label { font-size:10px; margin-top:4px; display:none; color:var(--text-muted); }

        /* Button */
        .btn-primary {
            width:100% !important; padding:14px !important;
            background:linear-gradient(90deg,#3b82f6,#8b5cf6) !important;
            color:white !important; border:none !important; border-radius:12px !important;
            font-size:15px !important; font-weight:600 !important;
            font-family:'Outfit',sans-serif !important;
            cursor:pointer !important; letter-spacing:0.5px;
            transition:all 0.3s ease !important;
            box-shadow:0 4px 15px rgba(59,130,246,0.3) !important;
            margin-top:8px;
        }
        .btn-primary:hover { transform:translateY(-2px) !important; box-shadow:0 6px 20px rgba(139,92,246,0.4) !important; }
        .btn-primary:disabled { opacity:0.7; cursor:not-allowed !important; transform:none !important; }

        /* Alert */
        .alert {
            border-radius:10px; padding:12px 15px; margin-bottom:22px !important;
            font-size:13px; display:flex; align-items:flex-start; gap:10px;
            animation:slideDown 0.4s ease; line-height:1.5;
        }
        @keyframes slideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
        .alert i { flex-shrink:0; margin-top:1px; }
        .alert-danger  { background:rgba(239,68,68,0.1) !important;  color:#ef4444 !important; border:1px solid rgba(239,68,68,0.2) !important; }
        .alert-success { background:rgba(16,185,129,0.1) !important; color:#10b981 !important; border:1px solid rgba(16,185,129,0.2) !important; }

        /* Back link */
        .back-link { text-align:center; margin-top:22px; }
        .back-link a {
            color:var(--text-muted); text-decoration:none; font-size:13px;
            font-weight:500; transition:color 0.3s;
            display:inline-flex; align-items:center; gap:6px;
        }
        .back-link a:hover { color:var(--primary-glow); }

        /* Divider & footer */
        .divider { border:none; border-top:1px solid var(--glass-border); margin:22px 0 16px; }
        .demo-info { text-align:center; color:var(--text-muted); font-size:11px; }

        @media(max-width:500px){ .card-body{ padding:28px 20px !important; } }
    </style>
</head>
<body>

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
            <div class="card-body">

                <!-- Icon -->
                <div class="icon-wrap">
                    <?php if ($token_valid): ?>
                        <i class="fas fa-key" style="font-size:28px;color:var(--primary-glow);"></i>
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle" style="font-size:28px;color:#ef4444;"></i>
                    <?php endif; ?>
                </div>

                <!-- Header -->
                <div class="form-header">
                    <h2><?php echo $token_valid ? 'Buat Password Baru' : 'Tautan Tidak Valid'; ?></h2>
                    <p>
                        <?php echo $token_valid
                            ? 'Masukkan password baru Anda. Pastikan menggunakan kombinasi yang kuat dan mudah diingat.'
                            : 'Tautan ini sudah kedaluwarsa atau tidak valid.'; ?>
                    </p>
                </div>

                <!-- Alert -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $msg_type; ?>">
                        <i class="fas <?php echo $msg_type=='danger' ? 'fa-times-circle' : 'fa-check-circle'; ?>"></i>
                        <div><?php echo $message; ?></div>
                    </div>
                <?php endif; ?>

                <!-- Form (hanya tampil jika token valid) -->
                <?php if ($token_valid): ?>
                <form method="POST" action="?token=<?php echo htmlspecialchars($token); ?>" id="resetForm">

                    <!-- Password Baru -->
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="new_password" name="new_password"
                                   class="form-control" placeholder="Min. 6 karakter" required>
                            <i class="fas fa-eye toggle-password" id="toggleNew"></i>
                        </div>
                        <div class="strength-bar-wrap" id="strengthBarWrap">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                        <div class="strength-label" id="strengthLabel"></div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <div class="input-group" id="wrapConfirm">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password"
                                   class="form-control" placeholder="Ulangi password baru" required>
                            <i class="fas fa-eye toggle-password" id="toggleConfirm"></i>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-shield-alt"></i> Simpan Password Baru
                    </button>
                </form>
                <?php else: ?>
                    <!-- Tombol minta link baru jika token tidak valid -->
                    <a href="forgot_password.php" class="btn-primary" style="display:block;text-align:center;text-decoration:none;padding:14px;">
                        <i class="fas fa-paper-plane"></i> Minta Tautan Baru
                    </a>
                <?php endif; ?>

                <div class="back-link">
                    <a href="index.php"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
                </div>

                <hr class="divider">
                <div class="demo-info">
                    <p style="margin:0;">&copy; <?php echo date('Y'); ?> PT. Sinergi Nusantara Integrasi</p>
                    <p style="margin:5px 0 0;font-size:10px;color:#aaa;letter-spacing:0.5px;">
                        Developed by <span style="color:#666;">Rahul Candra</span>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- Sinkron tema ---
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon      = themeToggleBtn.querySelector('i');
        const htmlEl         = document.documentElement;
        const savedTheme     = localStorage.getItem('theme');
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

        // --- Show/Hide Password ---
        function setupToggle(btnId, inputId) {
            const btn = document.getElementById(btnId);
            const inp = document.getElementById(inputId);
            if (!btn || !inp) return;
            btn.addEventListener('click', function () {
                const t = inp.type === 'password' ? 'text' : 'password';
                inp.setAttribute('type', t);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
        setupToggle('toggleNew',     'new_password');
        setupToggle('toggleConfirm', 'confirm_password');

        // --- Password Strength ---
        const passInput       = document.getElementById('new_password');
        const strengthBarWrap = document.getElementById('strengthBarWrap');
        const strengthBar     = document.getElementById('strengthBar');
        const strengthLabel   = document.getElementById('strengthLabel');

        if (passInput) {
            passInput.addEventListener('input', function () {
                const val = this.value;
                if (!val.length) {
                    strengthBarWrap.style.display = 'none';
                    strengthLabel.style.display   = 'none';
                    return;
                }
                strengthBarWrap.style.display = 'block';
                strengthLabel.style.display   = 'block';

                let score = 0;
                if (val.length >= 6)            score++;
                if (val.length >= 10)           score++;
                if (/[A-Z]/.test(val))          score++;
                if (/[0-9]/.test(val))          score++;
                if (/[^A-Za-z0-9]/.test(val))  score++;

                const levels = [
                    {w:'20%',  color:'#ef4444', label:'Sangat Lemah'},
                    {w:'40%',  color:'#f97316', label:'Lemah'},
                    {w:'60%',  color:'#eab308', label:'Cukup'},
                    {w:'80%',  color:'#3b82f6', label:'Kuat'},
                    {w:'100%', color:'#10b981', label:'Sangat Kuat'},
                ];
                const lv = levels[Math.min(score - 1, 4)] || levels[0];
                strengthBar.style.width      = lv.w;
                strengthBar.style.background = lv.color;
                strengthLabel.textContent    = 'Kekuatan: ' + lv.label;
                strengthLabel.style.color    = lv.color;
            });
        }

        // --- Validasi konfirmasi real-time ---
        const konfInput  = document.getElementById('confirm_password');
        const wrapKonf   = document.getElementById('wrapConfirm');

        if (konfInput) {
            konfInput.addEventListener('input', function () {
                if (!this.value.length) {
                    wrapKonf.querySelector('i:first-child').style.color = '';
                    return;
                }
                const match = this.value === passInput.value;
                wrapKonf.querySelector('i:first-child').style.color = match ? '#10b981' : '#ef4444';
            });
        }

        // --- Disable tombol saat submit ---
        const form      = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');
        if (form) {
            form.addEventListener('submit', function () {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';
            });
        }
    });
    </script>
</body>
</html>