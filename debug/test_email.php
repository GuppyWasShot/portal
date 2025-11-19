<?php
/**
 * Debug Email Sender
 * --------------------------------------
 * Simple utility to verify that PHP mail() works from this hosting environment.
 * Upload to server (keep in /debug) and access via browser.
 */

if (php_sapi_name() === 'cli') {
    echo "Buka file ini melalui browser untuk menjalankan tes.\n";
    exit;
}

function env_value($key) {
    return isset($_SERVER[$key]) ? $_SERVER[$key] : (isset($_ENV[$key]) ? $_ENV[$key] : null);
}

$default_to = 'nightmarish245@gmail.com';
$default_subject = 'Test Email Portal TPL';
$default_message = "Ini adalah email percobaan dari debug/test_email.php.\nTanggal: " . date('Y-m-d H:i:s');

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = filter_var(trim($_POST['to'] ?? ''), FILTER_VALIDATE_EMAIL);
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$to) {
        $error = 'Alamat email tujuan tidak valid.';
    } elseif ($subject === '' || $message === '') {
        $error = 'Subject dan pesan wajib diisi.';
    } else {
        $domain = $_SERVER['SERVER_NAME'] ?? 'portal-tpl.local';
        $headers = [];
        $headers[] = "From: Portal TPL Debug <no-reply@{$domain}>";
        $headers[] = "Reply-To: no-reply@{$domain}";
        $headers[] = "Content-Type: text/plain; charset=UTF-8";

        $sent = @mail($to, $subject, $message, implode("\r\n", $headers));

        if ($sent) {
            $result = "Email berhasil dikirim ke {$to}. Silakan cek inbox/spam.";
        } else {
            $error = "Gagal mengirim email menggunakan mail(). Periksa error log hosting atau aktifkan SMTP.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Debug Email Portal TPL</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #2d1b69; background: #f4f4fb; }
        h1 { color: #e30081; }
        form { background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); max-width: 600px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 12px; margin-bottom: 16px; border-radius: 8px; border: 1px solid #ddd; font-family: inherit; }
        textarea { min-height: 120px; resize: vertical; }
        button { padding: 12px 24px; background: #e30081; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .alert { margin: 20px 0; padding: 12px 16px; border-radius: 8px; }
        .alert.success { background: #e6f6ee; color: #1f7a4d; }
        .alert.error { background: #fdecea; color: #b21f1f; }
        .meta { margin-top: 30px; font-size: 14px; color: #555; }
        code { background: #eee; padding: 2px 4px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Debug Email Portal TPL</h1>
    <p>Gunakan form ini untuk memastikan fungsi <code>mail()</code> berjalan di hosting.</p>

    <?php if ($result): ?>
        <div class="alert success"><?php echo htmlspecialchars($result); ?></div>
    <?php elseif ($error): ?>
        <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="to">Email Tujuan</label>
        <input type="text" id="to" name="to" value="<?php echo htmlspecialchars($_POST['to'] ?? $default_to); ?>" required>

        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($_POST['subject'] ?? $default_subject); ?>" required>

        <label for="message">Pesan</label>
        <textarea id="message" name="message" required><?php echo htmlspecialchars($_POST['message'] ?? $default_message); ?></textarea>

        <button type="submit">Kirim Tes Email</button>
    </form>

    <div class="meta">
        <p><strong>Server Info:</strong></p>
        <ul>
            <li>Server Name: <?php echo htmlspecialchars(env_value('SERVER_NAME') ?? 'unknown'); ?></li>
            <li>PHP Version: <?php echo phpversion(); ?></li>
            <li>mail() tersedia: <?php echo function_exists('mail') ? 'Ya' : 'Tidak'; ?></li>
        </ul>
        <p>Jika mail() gagal, cek cPanel error log, atau gunakan SMTP (contoh: PHPMailer + Gmail/SMTP provider).</p>
    </div>
</body>
</html>

