<?php
// 1. SETTING NAMA FILE
$db_file = 'data.json';

// 2. LOGIKA PENYELAMAT (Biar data muncul)
if (!file_exists($db_file)) {
    file_put_contents($db_file, json_encode([], JSON_PRETTY_PRINT));
}

// Ambil isi file
$raw_content = file_get_contents($db_file);
$all_pages = json_decode($raw_content, true);

// Kalau JSON rusak atau isinya bukan array, paksa jadi array kosong
if (!is_array($all_pages)) {
    $all_pages = [];
}

// --- 3. LOGIKA VIEWER (Tampilkan Halaman) ---
$slug = isset($_GET['page']) ? trim($_GET['page'], '/') : null;
if ($slug && isset($all_pages[$slug])) {
    header("Content-Type: text/html; charset=UTF-8");
    echo $all_pages[$slug]['konten'];
    exit;
}

// --- 4. LOGIKA SIMPAN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slug'])) {
    $custom_slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_POST['slug']));
    
    if ($custom_slug) {
        // Masukkan data baru ke array yang sudah ada (biar gak ketimpa semua)
        $all_pages[$custom_slug] = [
            "judul" => htmlspecialchars($_POST['judul']),
            "konten" => $_POST['konten'], 
            "updated" => date("Y-m-d H:i")
        ];
        
        // Simpan SEMUA data balik ke data.json
        file_put_contents($db_file, json_encode($all_pages, JSON_PRETTY_PRINT));
        
        // Refresh dashboard
        header("Location: index.php"); 
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>NGID Web Builder Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #111; color: #fff; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; display: flex; gap: 20px; }
        .box { background: #222; padding: 20px; border-radius: 8px; flex: 1; border: 1px solid #444; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; background: #000; color: #0f0; border: 1px solid #444; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 200px; }
        button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; cursor: pointer; border-radius: 4px; font-weight: bold; }
        .card { background: #333; padding: 10px; margin-top: 10px; border-radius: 4px; border-left: 4px solid #007bff; }
        a { color: #00d4ff; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="box">
        <h3>➕ Buat Halaman</h3>
        <form method="POST">
            <input type="text" name="judul" placeholder="Judul" required>
            <input type="text" name="slug" placeholder="Slug (misal: bio)" required>
            <textarea name="konten" placeholder="Kode HTML..." required></textarea>
            <button type="submit">SIMPAN</button>
        </form>
    </div>

    <div class="box">
        <h3>📂 Daftar Halaman (Total: <?= count($all_pages) ?>)</h3>
        <?php if(empty($all_pages)): ?>
            <p style="color: #888;">Data masih kosong di data.json</p>
        <?php else: ?>
            <?php foreach (array_reverse($all_pages, true) as $s => $p): ?>
                <div class="card">
                    <strong><?= htmlspecialchars($p['judul']) ?></strong><br>
                    <a href="./<?= $s ?>" target="_blank">ngid.my.id/<?= $s ?></a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
