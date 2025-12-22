<?php
// 1. SETTING ERROR REPORTING (Biar gak langsung Error 500 kalau ada salah)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$database = 'data.json';

// 2. CEK & BUAT DATABASE JSON
if (!file_exists($database)) {
    file_put_contents($database, json_encode([], JSON_PRETTY_PRINT));
}

$all_pages = json_decode(file_get_contents($database), true) ?: [];

// 3. LOGIKA SIMPAN DATA
if (isset($_POST['save'])) {
    // Bersihkan slug (hanya huruf, angka, dan minus)
    $slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_POST['slug']));
    
    if (!empty($slug)) {
        $all_pages[$slug] = [
            "slug"   => $slug,
            "judul"  => $_POST['judul'],
            "konten" => $_POST['konten'] // Simpan mentah agar tag HTML tidak rusak
        ];

        if (file_put_contents($database, json_encode($all_pages, JSON_PRETTY_PRINT))) {
            header("Location: ./" . $slug);
            exit;
        } else {
            die("Error: Gagal nulis ke data.json. Cek permission file lo!");
        }
    }
}

// 4. LOGIKA TAMPILKAN HALAMAN (VIEWER)
$page_id = $_GET['page'] ?? null;
if ($page_id && isset($all_pages[$page_id])) {
    $site = $all_pages[$page_id];

    // PAKSA BROWSER JALANIN HTML (Biar gak jadi Plain Text)
    header("Content-Type: text/html; charset=UTF-8");
    
    // Keluarin konten mentah (Raw HTML)
    echo $site['konten']; 
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Site Builder - Dashboard</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #eef2f3; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        input[type="text"], textarea { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        textarea { height: 350px; font-family: 'Courier New', Courier, monospace; background: #272822; color: #f8f8f2; }
        button { background: #3498db; color: white; border: none; padding: 12px 25px; border-radius: 6px; cursor: pointer; font-size: 16px; margin-top: 20px; transition: 0.3s; }
        button:hover { background: #2980b9; }
        .list-section { margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px; }
        .web-card { background: #f9f9f9; padding: 10px; border-left: 4px solid #3498db; margin-bottom: 10px; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h1>🚀 Website Engine</h1>
    <p>Paste kode HTML lengkap lo (termasuk tag &lt;html&gt;, &lt;head&gt;, &lt;body&gt;) untuk menerbitkan halaman baru.</p>

    <form method="POST">
        <label>Judul Project:</label>
        <input type="text" name="judul" placeholder="Contoh: My Landing Page" required>

        <label>URL Slug (Nama URL):</label>
        <input type="text" name="slug" placeholder="misal: web-gue" required>

        <label>Kode HTML Konten:</label>
        <textarea name="konten" placeholder="<html>
<body>
  <h1>Hello World!</h1>
</body>
</html>" required></textarea>

        <button type="submit" name="save">Terbitkan Website</button>
    </form>

    <div class="list-section">
        <h3>Daftar Website Terbit:</h3>
        <?php if (empty($all_pages)): ?>
            <p style="color: #999;">Belum ada website yang dibuat.</p>
        <?php else: ?>
            <?php foreach ($all_pages as $slug => $data): ?>
                <div class="web-card">
                    <strong><?= htmlspecialchars($data['judul']) ?></strong><br>
                    Link: <a href="./<?= $slug ?>" target="_blank">/<?= $slug ?></a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>