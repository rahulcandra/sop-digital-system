<?php
session_start();
require_once 'config/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$message  = '';
$msg_type = '';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$dir      = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base_url = $protocol . '://' . $host . $dir;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg_type = 'danger';
        $message  = 'Format email tidak valid.';
    } else {
        $stmt = $conn->prepare("SELECT id, nama_lengkap, email FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user  = $result->fetch_assoc();
            $token = bin2hex(random_bytes(50));

            date_default_timezone_set('Asia/Jakarta');
            $expire_time = date("Y-m-d H:i:s", strtotime('+1 hour'));

            $upd = $conn->prepare("UPDATE users SET reset_token = ?, token_expire = ? WHERE id = ?");
            $upd->bind_param("ssi", $token, $expire_time, $user['id']);
            $upd->execute();

            $reset_link = $base_url . '/reset_password.php?token=' . urlencode($token);

            $subject = "Permintaan Reset Password - SOP Digital";

            $email_body  = "Halo " . $user['nama_lengkap'] . ",\n\n";
            $email_body .= "Kami menerima permintaan untuk mereset password akun SOP Digital Anda.\n";
            $email_body .= "Silakan klik tautan berikut untuk membuat password baru:\n\n";
            $email_body .= $reset_link . "\n\n";
            $email_body .= "Tautan ini hanya berlaku selama 1 jam.\n";
            $email_body .= "Jika Anda tidak meminta reset password, abaikan email ini.\n\n";
            $email_body .= "Terima kasih,\nTim IT Sinergi Nusantara Integrasi";

            // ✅ PHPMailer via Gmail SMTP
            $mail = new PHPMailer(true);
            $mail_sent = false;

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'rahulcandra19@gmail.com';  // ← Gmail Anda
                $mail->Password   = 'ooxc vbww vxwm dcbn';        // ← Ganti dengan App Password 16 karakter
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('rahulcandra19@gmail.com', 'SOP Digital - Sinergi');
                $mail->addAddress($user['email'], $user['nama_lengkap']);

                $mail->isHTML(false);
                $mail->Subject = $subject;
                $mail->Body    = $email_body;

                $mail->send();
                $mail_sent = true;

            } catch (Exception $e) {
                $mail_sent = false;
                error_log("Mailer Error: " . $mail->ErrorInfo);
            }

            if ($mail_sent) {
                $msg_type = 'success';
                $message  = 'Tautan reset password telah dikirim ke email Anda. Silakan cek <b>Inbox</b> atau folder <b>Spam</b>.';
            } else {
                // Fallback mode testing
                $msg_type = 'warning';
                $message  = 'Gagal mengirim email. Periksa konfigurasi SMTP.<br><br>'
                          . '<b>[MODE TESTING]</b> Gunakan tautan ini langsung:<br>'
                          . '<a href="' . htmlspecialchars($reset_link) . '" style="color:inherit;font-weight:600;text-decoration:underline;" target="_blank">'
                          . '🔗 Klik di sini untuk reset password</a>';
            }
        } else {
            $msg_type = 'info';
            $message  = 'Jika email tersebut terdaftar, tautan reset telah dikirimkan. Silakan cek Inbox Anda.';
        }
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
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            overflow-x: hidden;
            transition: background-color 0.5s ease, color 0.5s ease;
        }

        .ambient-light {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background:
                radial-gradient(circle at 15% 50%, rgba(59,130,246,0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(249,115,22,0.08), transparent 25%);
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

        .login-container {
            width: 100%; padding: 20px;
            display: flex; justify-content: center; align-items: center;
            z-index: 10;
        }

        .login-box {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3), 0 0 40px rgba(59,130,246,0.08);
            max-width: 460px;
            width: 100%;
            overflow: hidden;
            animation: cardEntrance 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
            transition: background 0.5s ease, border-color 0.5s ease;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .login-right {
            padding: 45px 40px;
            display: flex; flex-direction: column; justify-content: center;
        }

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

        .form-header { text-align: center; margin-bottom: 28px; }
        .form-header h2 {
            color: var(--text-main);
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            letter-spacing: -0.5px;
        }
        .form-header p {
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        .form-group { margin-bottom: 22px; position: relative; }
        .form-group label {
            color: var(--text-main);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: block;
            margin-left: 4px;
        }

        .input-group { position: relative; width: 100%; }
        .input-group i.icon-left {
            position: absolute; left: 16px; top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted); font-size: 15px;
            transition: 0.3s; z-index: 2;
        }

        .form-control {
            width: 100%;
            padding: 14px 14px 14px 45px;
            background: var(--input-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 14px;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s ease;
        }
        .form-control::placeholder { color: var(--text-muted); font-weight: 300; }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-glow);
            background: var(--glass-bg);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59,130,246,0.3);
            margin-top: 5px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139,92,246,0.4);
        }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 22px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            backdrop-filter: blur(10px);
            animation: slideDown 0.4s ease;
            line-height: 1.6;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert i { flex-shrink: 0; margin-top: 2px; }

        .alert-danger  { background: rgba(239,68,68,0.1);   color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
        .alert-success { background: rgba(16,185,129,0.1);  color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
        .alert-warning { background: rgba(249,115,22,0.1);  color: #f97316; border: 1px solid rgba(249,115,22,0.2); }
        .alert-info    { background: rgba(59,130,246,0.1);  color: #60a5fa; border: 1px solid rgba(59,130,246,0.2); }
        .alert-warning a, .alert-info a { color: inherit; }

        .back-link { text-align: center; margin-top: 24px; }
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

        .divider {
            border: none;
            border-top: 1px solid var(--glass-border);
            margin: 22px 0 16px;
        }
        .demo-info { text-align: center; color: var(--text-muted); font-size: 11px; }

        @media (max-width: 500px) {
            .login-right { padding: 30px 22px; }
        }
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
        <div class="login-right">

            <div class="icon-wrap">
                <i class="fas fa-unlock-alt" style="font-size:28px; color:var(--primary-glow);"></i>
            </div>

            <div class="form-header">
                <h2>Lupa Password?</h2>
                <p>Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang password.</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo htmlspecialchars($msg_type); ?>">
                    <i class="fas <?php
                        echo match($msg_type) {
                            'success' => 'fa-check-circle',
                            'warning' => 'fa-exclamation-triangle',
                            'info'    => 'fa-info-circle',
                            default   => 'fa-times-circle'
                        };
                    ?>"></i>
                    <div><?php echo $message; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="forgotForm">
                <div class="form-group">
                    <label for="email">Alamat Email Terdaftar</label>
                    <div class="input-group">
                        <i class="fas fa-envelope icon-left"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="contoh: karyawan@sinergi.co.id"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Kirim Link Reset
                </button>
            </form>

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

    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeIcon      = themeToggleBtn.querySelector('i');
    const htmlEl         = document.documentElement;

    if (localStorage.getItem('theme') === 'light') {
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