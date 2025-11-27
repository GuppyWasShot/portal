<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cache Clear Utility - InfinityFree</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #333; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔄 Cache Clear Utility</h1>
    <p>Utility untuk membersihkan cache di hosting InfinityFree</p>

    <?php
    // Clear PHP OPcache if enabled
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo '<div class="success">✅ OPcache cleared successfully!</div>';
    } else {
        echo '<div class="info">ℹ️ OPcache not enabled on this server</div>';
    }

    // Clear file stat cache
    clearstatcache();
    echo '<div class="success">✅ File stat cache cleared!</div>';

    // Force browser cache clear with headers
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    
    // Generate cache-busting timestamp
    $cache_version = time();
    echo '<div class="success">✅ Browser cache headers set!</div>';
    ?>

    <div class="info">
        <h3>📝 Cache Busting untuk Assets</h3>
        <p>Tambahkan versioning pada CSS/JS Anda:</p>
        <code>&lt;link href="assets/css/styles.css?v=<?php echo $cache_version; ?>" rel="stylesheet"&gt;</code>
    </div>

    <div class="info">
        <h3>🌐 InfinityFree Specific</h3>
        <p>InfinityFree menggunakan CloudFlare caching. Untuk clear cache:</p>
        <ol>
            <li>Login ke control panel InfinityFree</li>
            <li>Buka "Cloudflare Settings"</li>
            <li>Klik "Purge Cache" atau "Clear All Cache"</li>
        </ol>
    </div>

    <div class="warning">
        <h3>⚠️ Perbaikan Foto Dosen</h3>
        <p><strong>Path Issue:</strong> Foto menggunakan path relatif <code>../../uploads/dosen/</code></p>
        <p><strong>Solusi:</strong></p>
        <ol>
            <li>Gunakan absolute path dari document root</li>
            <li>Atau gunakan <code>$_SERVER['DOCUMENT_ROOT']</code></li>
            <li>Upload foto langsung ke folder <code>public_html/uploads/dosen/</code> di hosting</li>
        </ol>
    </div>

    <p><a href="index.php">← Kembali ke Beranda</a></p>

    <script>
        // Force reload page without cache
        if (performance.navigation.type !== 2) {
            // Not a back/forward navigation
            location.reload(true);
        }
    </script>
</body>
</html>
