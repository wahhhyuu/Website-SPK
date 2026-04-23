<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/fuzzy.php';
requireLogin();
if (!isset($_SESSION['hasil'])) redirect('index.php');

$pageTitle = 'Normalisasi';
$h    = $_SESSION['hasil'];
$norm = $h['normalized'];
$maxX = $h['maxX'];
$W    = WEIGHTS;
$krKeys = array_keys(KRITERIA);
require_once 'includes/header.php';
?>

<div class="page-hero">
  <div class="hero-tag">Langkah 3 dari 4</div>
  <h1>Normalisasi &amp; Perhitungan Vi</h1>
  <p>Normalisasi benefit menggunakan floor ke 2 desimal, kemudian dikalikan bobot untuk mendapatkan nilai preferensi Vi.</p>
</div>

<!-- Rumus -->
<div class="card">
  <div class="card-header">
    <div class="card-icon" style="background:rgba(163,113,247,0.12)">📐</div>
    <div>
      <div class="card-title">Formula Normalisasi &amp; Preferensi</div>
      <div class="card-sub">Metode Benefit — semua atribut semakin besar semakin baik</div>
    </div>
  </div>
  <div class="card-body">
    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:18px 20px;font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--muted);line-height:1.9">
      <span style="color:var(--accent2)">// Normalisasi (Benefit) — floor ke 2 desimal</span><br>
      <span style="color:var(--text)">r_ij = floor( x_ij / max(x_j) × 100 ) / 100</span><br><br>
      <span style="color:var(--accent2)">// Komponen Vi — floor ke 2 desimal per item</span><br>
      <span style="color:var(--text)">comp_j = floor( w_j × r_ij × 100 ) / 100</span><br><br>
      <span style="color:var(--accent2)">// Nilai Preferensi Akhir</span><br>
      <span style="color:var(--text)">V_i = comp_1 + comp_2 + ... + comp_7</span><br><br>
      <span style="color:var(--gold)">// Bobot W = [<?= implode(', ', $W) ?>]</span>
    </div>

    <div class="info-box" style="margin-top:16px">
      <span class="ico">📌</span>
      <div>
        <strong style="color:var(--text)">Nilai Max per Kriteria:</strong><br>
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px">
          <?php foreach ($krKeys as $j => $k): ?>
          <span class="badge badge-orange mono"><?= $k ?> = <?= fmt($maxX[$j]) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Matriks R -->
<div class="card">
  <div class="card-header">
    <div class="card-icon" style="background:rgba(63,185,80,0.12)">🔢</div>
    <div>
      <div class="card-title">Matriks Ternormalisasi R</div>
      <div class="card-sub">r_ij = floor(x_ij / max(x_j) × 100) / 100</div>
    </div>
  </div>
  <div class="card-body">
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama</th>
            <?php foreach ($krKeys as $k): ?><th>r_<?= $k ?></th><?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($norm as $d): ?>
          <tr>
            <td><span class="badge badge-blue mono"><?= e($d['kode']) ?></span></td>
            <td style="font-size:13px"><?= e($d['nama']) ?></td>
            <?php foreach ($d['R'] as $r): ?>
            <td class="mono" style="color:<?= $r >= 0.9 ? 'var(--accent3)' : ($r >= 0.7 ? 'var(--accent2)' : 'var(--muted)') ?>">
              <?= fmt($r) ?>
            </td>
            <?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Perhitungan Vi -->
<div class="card">
  <div class="card-header">
    <div class="card-icon" style="background:rgba(240,136,62,0.12)">🧮</div>
    <div>
      <div class="card-title">Perhitungan Nilai Preferensi Vi</div>
      <div class="card-sub">comp_j = floor(w_j × r_ij × 100)/100 &nbsp;|&nbsp; Vi = Σ comp_j</div>
    </div>
  </div>
  <div class="card-body">
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama</th>
            <?php foreach ($krKeys as $j => $k): ?>
            <th style="font-size:10px"><?= fmt($W[$j]) ?>×r_<?= $k ?></th>
            <?php endforeach; ?>
            <th style="color:var(--accent)">Vi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($norm as $d):
            $comps = array_map(fn($r, $w) => fl($w * $r), $d['R'], $W);
            $Vi    = round(array_sum($comps), 2);
          ?>
          <tr>
            <td><span class="badge badge-blue mono"><?= e($d['kode']) ?></span></td>
            <td style="font-size:13px"><?= e($d['nama']) ?></td>
            <?php foreach ($comps as $c): ?>
            <td class="mono" style="font-size:12px;color:var(--muted)"><?= fmt($c) ?></td>
            <?php endforeach; ?>
            <td class="mono" style="color:var(--accent);font-weight:700;font-size:15px"><?= fmt($Vi) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="btn-row">
      <a href="hasil.php" class="btn btn-success">🏆 Lihat Hasil Ranking</a>
      <a href="fuzzifikasi.php" class="btn btn-secondary">← Kembali</a>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
