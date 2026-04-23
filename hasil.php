<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/fuzzy.php';
requireLogin();
if (!isset($_SESSION['hasil'])) redirect('index.php');

$pageTitle = 'Hasil Ranking';
$ranked    = $_SESSION['hasil']['ranked'];
$norm      = $_SESSION['hasil']['normalized'];
$W         = WEIGHTS;
$krKeys    = array_keys(KRITERIA);
$top3c     = ['gold', 'silver', 'bronze'];
require_once 'includes/header.php';
?>

<div class="page-hero">
  <div class="hero-tag">Langkah 4 dari 4</div>
  <h1>Hasil &amp; Peringkat Akhir</h1>
  <p>Dosen terbaik berdasarkan Fuzzy SAW — diurutkan dari nilai preferensi tertinggi.</p>
</div>

<!-- Top 3 -->
<div class="card">
  <div class="card-header">
    <div class="card-icon" style="background:rgba(227,179,65,0.12)">🥇</div>
    <div>
      <div class="card-title">Top 3 Dosen Terbaik</div>
      <div class="card-sub">Nilai preferensi tertinggi</div>
    </div>
    <div class="card-actions">
      <button onclick="window.print()" class="btn btn-secondary btn-sm">🖨 Cetak</button>
    </div>
  </div>
  <div class="card-body">
    <div class="rank-grid">
      <?php foreach (array_slice($ranked, 0, 3) as $i => $d):
        $pct = round($d['Vi'] * 100, 0); ?>
      <div class="rank-card <?= $top3c[$i] ?>">
        <div class="rank-medal"><?= medal($i + 1) ?></div>
        <div class="rank-pos-label">Peringkat <?= $i + 1 ?></div>
        <div class="rank-kode"><?= e($d['kode']) ?></div>
        <div class="rank-nama"><?= e($d['nama']) ?></div>
        <div class="rank-score"><?= fmt($d['Vi']) ?></div>
        <div class="rank-bar"><div class="rank-fill" style="width:<?= $pct ?>%"></div></div>
        <div style="font-size:11px;color:var(--muted);margin-top:5px"><?= $pct ?>% skor</div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Tabel Lengkap -->
<div class="card">
  <div class="card-header">
    <div class="card-icon" style="background:rgba(88,166,255,0.12)">📋</div>
    <div>
      <div class="card-title">Tabel Peringkat Lengkap</div>
      <div class="card-sub">Semua alternatif diurutkan berdasarkan nilai Vi</div>
    </div>
  </div>
  <div class="card-body">
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Peringkat</th><th>Kode</th><th>Nama Dosen</th>
            <th>C1</th><th>C2</th><th>C3</th><th>C4</th>
            <th>C5</th><th>C6</th><th>C7</th>
            <th>Nilai Vi</th><th>Skor</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ranked as $d):
            $pct = round($d['Vi'] * 100, 1); ?>
          <tr <?= $d['rank'] === 1 ? 'class="highlight"' : '' ?>>
            <td class="mono" style="font-weight:700;color:<?= $d['rank']===1?'var(--gold)':($d['rank']<=3?'var(--accent2)':'var(--muted)') ?>">
              <?= medal($d['rank']) ?> <?= $d['rank'] ?>
            </td>
            <td><span class="badge badge-blue mono"><?= e($d['kode']) ?></span></td>
            <td style="font-weight:<?= $d['rank']===1?600:400 ?>"><?= e($d['nama']) ?></td>
            <td class="mono" style="font-size:12px;color:var(--muted)"><?= $d['c1'] ?></td>
            <td class="mono" style="font-size:12px;color:var(--muted)"><?= $d['c2'] ?></td>
            <td class="mono" style="font-size:12px;color:var(--muted)"><?= number_format($d['c3'],2) ?></td>
            <td class="mono" style="font-size:12px;color:var(--muted)"><?= $d['c4'] ?> th</td>
            <td class="mono" style="font-size:12px;color:var(--muted)"><?= $d['c5'] ?></td>
            <td class="mono" style="font-size:12px;color:var(--muted)"><?= $d['c6'] ?></td>
            <td class="mono" style="font-size:12px;color:var(--muted)"><?= $d['c7'] ?></td>
            <td class="mono" style="color:var(--accent);font-weight:700;font-size:15px"><?= fmt($d['Vi']) ?></td>
            <td>
              <div class="skor-bar-wrap">
                <div class="skor-bar-bg"><div class="skor-bar-fill" style="width:<?= $pct ?>%"></div></div>
                <div class="skor-pct"><?= $pct ?>%</div>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <hr class="div">

    <!-- Kesimpulan -->
    <?php $win = $ranked[0]; $last = end($ranked); ?>
    <div class="alert alert-success">
      ✅ <strong>Kesimpulan:</strong> Berdasarkan Fuzzy SAW,
      <strong><?= e($win['kode']) ?> — <?= e($win['nama']) ?></strong>
      mendapat nilai preferensi tertinggi <strong><?= fmt($win['Vi']) ?></strong>
      dan dinobatkan sebagai <strong>Dosen Terbaik</strong>.
      <?= e($last['kode']) ?> (Vi = <?= fmt($last['Vi']) ?>) mendapat peringkat terendah.
    </div>

    <div class="btn-row">
      <a href="index.php" class="btn btn-secondary">↺ Hitung Ulang</a>
      <a href="normalisasi.php" class="btn btn-secondary">← Normalisasi</a>
      <button onclick="window.print()" class="btn btn-primary">🖨 Cetak Hasil</button>
    </div>
  </div>
</div>

<!-- Rekap Vi -->
<div class="card">
  <div class="card-header">
    <div class="card-icon" style="background:rgba(240,136,62,0.12)">📊</div>
    <div>
      <div class="card-title">Rekap Nilai Preferensi </div>
      <div class="card-sub">Nilai Vi sebelum diurutkan</div>
    </div>
  </div>
  <div class="card-body">
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Alternatif</th>
            <?php foreach ($norm as $d): ?>
            <td style="text-align:center;padding:8px 10px;background:var(--surface2);border-bottom:1px solid var(--border)">
              <span class="badge badge-blue mono"><?= e($d['kode']) ?></span>
            </td>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="mono" style="font-weight:600;color:var(--accent)">Nilai Vi</td>
            <?php foreach ($norm as $d):
              $comps = array_map(fn($r, $w) => fl($w * $r), $d['R'], $W);
              $Vi    = round(array_sum($comps), 2);
            ?>
            <td class="mono" style="text-align:center;color:var(--accent)"><?= fmt($Vi) ?></td>
            <?php endforeach; ?>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
