<?php
session_start();
require_once 'includes/config.php';

// Redirect jika sudah login
if (isLoggedIn()) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password tidak boleh kosong.';
    } else {
        $md5pass = md5($password);
        $st = getDB()->prepare("SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1");
        $st->execute([$username, $md5pass]);
        $user = $st->fetch();

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];
            redirect('index.php');
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — SPK Dosen Terbaik</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="login-logo">
      <div class="login-logo-icon">🎓</div>
      <h1>SPK Dosen Terbaik</h1>
      <p>Sistem Pendukung Keputusan · Fuzzy SAW</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control <?= $error?'is-invalid':'' ?>"
               value="<?= e($_POST['username'] ?? '') ?>"
               placeholder="Masukkan username" autofocus required>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control <?= $error?'is-invalid':'' ?>"
               placeholder="Masukkan password" required>
        <div class="form-hint">Password dienkripsi dengan MD5</div>
      </div>

      <button type="submit" class="login-btn">🔑 Masuk ke Sistem</button>
    </form>

    <div class="login-divider">Info Default</div>
    <div class="info-box" style="margin-top:0;font-size:12px">
      <span class="ico">💡</span>
      <div>Default: username <strong>Lupa?</strong> / password <strong>Lupa?</strong></div>
    </div>
  </div>
</div>
</body>
</html>
