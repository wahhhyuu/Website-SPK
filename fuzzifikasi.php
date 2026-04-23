<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/fuzzy.php';
requireLogin();
if (!isset($_SESSION['hasil'])) redirect('index.php');

$pageTitle = 'Fuzzifikasi';
$fuzz = $_SESSION['hasil']['fuzzResult'];
require_once 'includes/header.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.css">
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/contrib/auto-render.min.js"
  onload="renderMathInElement(document.body,{delimiters:[{left:'$$',right:'$$',display:true},{left:'$',right:'$',display:false}],throwOnError:false});"></script>

<?php

require_once 'includes/fuzzy.php';
if (!isset($_SESSION['hasil'])) {
  header('Location: index.php');
  exit;
}
$pageTitle = 'Fuzzifikasi';
$fuzz = $_SESSION['hasil']['fuzzResult'];
require_once 'includes/header.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.css">
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/katex.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/KaTeX/0.16.9/contrib/auto-render.min.js"
  onload="renderMathInElement(document.body,{delimiters:[{left:'$$',right:'$$',display:true},{left:'$',right:'$',display:false}],throwOnError:false});">
</script>
<style>
  .math-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px
  }

  @media(max-width:800px) {
    .math-grid {
      grid-template-columns: 1fr
    }
  }

  .mcard {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 18px 20px
  }

  .mcard-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--accent2);
    margin-bottom: 14px;
    padding-bottom: 9px;
    border-bottom: 1px solid var(--border)
  }

  .mblock {
    margin-bottom: 12px;
    padding: 10px 14px;
    background: var(--bg);
    border-radius: 8px;
    border-left: 3px solid var(--border)
  }

  .mblock:hover {
    border-left-color: var(--accent)
  }

  .mlabel {
    font-size: 10px;
    color: var(--muted);
    margin-bottom: 5px;
    font-family: 'JetBrains Mono', monospace;
    text-transform: uppercase;
    letter-spacing: .5px
  }

  .katex {
    color: var(--text) !important;
    font-size: 1em !important
  }

  .mbobot {
    margin-top: 10px;
    padding: 7px 12px;
    background: rgba(227, 179, 65, .08);
    border: 1px solid rgba(227, 179, 65, .2);
    border-radius: 6px;
    font-size: 12px;
    color: var(--gold);
    font-family: 'JetBrains Mono', monospace
  }
</style>

<div class="container">
  <div class="hero" style="padding-bottom:24px">
    <div class="hero-tag">Langkah 2 dari 4</div>
    <h1>Proses Fuzzifikasi</h1>
    <p>Konversi nilai crisp ke derajat keanggotaan fuzzy. Nilai dibulatkan ke 2 desimal (floor/truncate).</p>
  </div>

  <!-- FUNGSI KEANGGOTAAN -->
  <div class="card">
    <div class="card-header">
      <div class="card-icon" style="background:rgba(88,166,255,0.12)">📐</div>
      <div>
        <div class="card-title">Fungsi Keanggotaan Fuzzy</div>
        <div class="card-sub">Triangular &amp; Trapezoidal Membership Functions</div>
      </div>
    </div>
    <div class="card-body">
      <div class="math-grid">

        <div class="mcard">
          <div class="mcard-title">🟢 C1 — Kedisiplinan (Kehadiran: 1–250)</div>

          <div class="mblock">
            <div class="mlabel">μ Rendah(X)</div>
            $$\mu_{Rendah}(X) =
            \begin{cases}
            1 & \text{jika } X \leq 50\\[6pt]
            \dfrac{125 - X}{75} & \text{jika } 50 < X < 125\\[6pt]
              0 & \text{jika } X \geq 125
              \end{cases}$$
              </div>

              <div class="mblock">
                <div class="mlabel">μ Sedang(X)</div>
                $$\mu_{Sedang}(X) =
                \begin{cases}
                0 & \text{jika } X \leq 50\\[6pt]
                \dfrac{X - 50}{75} & \text{jika } 50 < X < 125\\[6pt]
                  \dfrac{200 - X}{75} & \text{jika } 125 \leq X < 200\\[6pt]
                  0 & \text{jika } X \geq 200
                  \end{cases}$$
                  </div>

                  <div class="mblock">
                    <div class="mlabel">μ Tinggi(X)</div>
                    $$\mu_{Tinggi}(X) =
                    \begin{cases}
                    0 & \text{jika } X \leq 125\\[6pt]
                    \dfrac{X - 125}{75} & \text{jika } 125 < X < 200\\[6pt]
                      1 & \text{jika } X \geq 200
                      \end{cases}$$
                      </div>

                      <div class="mbobot">Bobot: Rendah=0.50 | Sedang=0.75 | Tinggi=1.00</div>
                  </div>


                  <div class="mcard">
                    <div class="mcard-title">🔵 C2 — Penelitian / Sitasi (100–700)</div>

                    <div class="mblock">
                      <div class="mlabel">μ Sedikit(X)</div>
                      $$\mu_{Sedikit}(X) =
                      \begin{cases}
                      1 & \text{jika } X \leq 100\\[6pt]
                      \dfrac{350 - X}{250} & \text{jika } 100 < X < 350\\[6pt]
                        0 & \text{jika } X \geq 350
                        \end{cases}$$
                        </div>

                        <div class="mblock">
                          <div class="mlabel">μ Sedang(X)</div>
                          $$\mu_{Sedang}(X) =
                          \begin{cases}
                          0 & \text{jika } X \leq 100 \text{ atau } X \geq 700\\[6pt]
                          \dfrac{X - 100}{250} & \text{jika } 100 < X < 350\\[6pt]
                            \dfrac{700 - X}{350} & \text{jika } 350 \leq X \leq 700
                            \end{cases}$$
                            </div>

                            <div class="mblock">
                              <div class="mlabel">μ Banyak(X)</div>
                              $$\mu_{Banyak}(X) =
                              \begin{cases}
                              0 & \text{jika } X \leq 350\\[6pt]
                              \dfrac{X - 350}{350} & \text{jika } 350 < X < 700\\[6pt]
                                1 & \text{jika } X \geq 700
                                \end{cases}$$
                                </div>

                                <div class="mbobot">Bobot: Sedikit=0.50 | Sedang=0.75 | Banyak=1.00</div>
                            </div>


                            <div class="mcard">
                              <div class="mcard-title">🟡 C3 — Penilaian Mahasiswa (1–4)</div>

                              <div class="mblock">
                                <div class="mlabel">μ Cukup(X)</div>
                                $$\mu_{Cukup}(X) =
                                \begin{cases}
                                1 & \text{jika } X \leq 1\\[6pt]
                                \dfrac{2.5 - X}{1.5} & \text{jika } 1 < X < 2.5\\[6pt]
                                  0 & \text{jika } X \geq 2.5
                                  \end{cases}$$
                                  </div>

                                  <div class="mblock">
                                    <div class="mlabel">μ Baik(X)</div>
                                    $$\mu_{Baik}(X) =
                                    \begin{cases}
                                    0 & \text{jika } X \leq 1 \text{ atau } X \geq 4\\[6pt]
                                    \dfrac{X - 1}{1.5} & \text{jika } 1 < X \leq 2.5\\[6pt]
                                      \dfrac{4 - X}{1.5} & \text{jika } 2.5 < X < 4
                                      \end{cases}$$
                                      </div>

                                      <div class="mblock">
                                        <div class="mlabel">μ Sangat Baik(X)</div>
                                        $$\mu_{SangatBaik}(X) =
                                        \begin{cases}
                                        0 & \text{jika } X \leq 2.5\\[6pt]
                                        \dfrac{X - 2.5}{1.5} & \text{jika } 2.5 < X < 4\\[6pt]
                                          1 & \text{jika } X \geq 4
                                          \end{cases}$$
                                          </div>

                                          <div class="mbobot">Bobot: Cukup=0.50 | Baik=0.75 | Sangat Baik=1.00</div>
                                      </div>


                                      <div class="mcard">
                                        <div class="mcard-title">🟠 C4 — Masa Kerja (1–11 tahun)</div>

                                        <div class="mblock">
                                          <div class="mlabel">μ Rendah(X)</div>
                                          $$\mu_{Rendah}(X) =
                                          \begin{cases}
                                          1 & \text{jika } X \leq 1\\[6pt]
                                          \dfrac{6 - X}{5} & \text{jika } 1 < X < 6\\[6pt]
                                            0 & \text{jika } X \geq 6
                                            \end{cases}$$
                                            </div>

                                            <div class="mblock">
                                              <div class="mlabel">μ Sedang(X)</div>
                                              $$\mu_{Sedang}(X) =
                                              \begin{cases}
                                              \dfrac{X - 1}{5} & \text{jika } 1 \leq X \leq 6\\[6pt]
                                              \dfrac{11 - X}{5} & \text{jika } 6 < X \leq 11\\[6pt]
                                                0 & \text{lainnya}
                                                \end{cases}$$
                                                </div>

                                                <div class="mblock">
                                                  <div class="mlabel">μ Tinggi(X)</div>
                                                  $$\mu_{Tinggi}(X) =
                                                  \begin{cases}
                                                  0 & \text{jika } X \leq 6\\[6pt]
                                                  \dfrac{X - 6}{5} & \text{jika } 6 < X < 11\\[6pt]
                                                    1 & \text{jika } X \geq 11
                                                    \end{cases}$$
                                                    </div>

                                                    <div class="mbobot">Bobot: Rendah=0.50 | Sedang=0.75 | Tinggi=1.00</div>
                                                </div>

                                            </div>
                                        </div>
                                      </div>

                                      <!-- TABEL FUZZ C1 -->
                                      <?php
                                      $fuzzTables = [
                                        ['title' => 'C1 — Kedisiplinan', 'sub' => 'μRendah | μSedang | μTinggi', 'bg' => 'rgba(63,185,80,0.12)', 'cols' => ['μRendah', 'μSedang', 'μTinggi'], 'key' => 'f1', 'mkeys' => ['muR', 'muS', 'muT'], 'raw' => 'c1'],
                                        ['title' => 'C2 — Penelitian (Sitasi)', 'sub' => 'μSedikit | μSedang | μBanyak', 'bg' => 'rgba(88,166,255,0.12)', 'cols' => ['μSedikit', 'μSedang', 'μBanyak'], 'key' => 'f2', 'mkeys' => ['muSd', 'muSe', 'muB'], 'raw' => 'c2'],
                                        ['title' => 'C3 — Penilaian Mahasiswa', 'sub' => 'μCukup | μBaik | μSangat Baik', 'bg' => 'rgba(227,179,65,0.12)', 'cols' => ['μCukup', 'μBaik', 'μSangat Baik'], 'key' => 'f3', 'mkeys' => ['muC', 'muB', 'muSB'], 'raw' => 'c3'],
                                        ['title' => 'C4 — Masa Kerja', 'sub' => 'μRendah | μSedang | μTinggi', 'bg' => 'rgba(240,136,62,0.12)', 'cols' => ['μRendah', 'μSedang', 'μTinggi'], 'key' => 'f4', 'mkeys' => ['muR', 'muS', 'muT'], 'raw' => 'c4'],
                                      ];
                                      foreach ($fuzzTables as $t): ?>
                                        <div class="card">
                                          <div class="card-header">
                                            <div class="card-icon" style="background:<?= $t['bg'] ?>">📊</div>
                                            <div>
                                              <div class="card-title">Hasil Fuzzifikasi <?= $t['title'] ?></div>
                                              <div class="card-sub"><?= $t['sub'] ?></div>
                                            </div>
                                          </div>
                                          <div class="card-body">
                                            <div class="tbl-wrap">
                                              <table>
                                                <thead>
                                                  <tr>
                                                    <th>Kode</th>
                                                    <th>Nilai Asli</th>
                                                    <?php foreach ($t['cols'] as $c): ?><th><?= $c ?></th><?php endforeach; ?>
                                                    <th>Kategori</th>
                                                    <th>Best μ</th>
                                                    <th>Nilai Fuzzy</th>
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                  <?php foreach ($fuzz as $d):
                                                    $f = $d[$t['key']]; ?>
                                                    <tr>
                                                      <td><span class="badge badge-blue mono"><?= $d['kode'] ?></span></td>
                                                     <td class="mono"><?= rtrim(rtrim($d[$t['raw']], '0'), '.') ?></td>
                                                      <?php foreach ($t['mkeys'] as $mk): ?>
                                                        <td class="mono"><?= fmt($f[$mk]) ?></td>
                                                      <?php endforeach; ?>
                                                      <td><span class="badge-kat <?= katClass($f['kat']) ?>"><?= $f['kat'] ?></span></td>
                                                      <td class="mono" style="color:var(--accent2)"><?= fmt($f['bestMu']) ?></td>
                                                      <td class="mono" style="color:var(--accent);font-weight:700"><?= fmt($f['fuzzy']) ?></td>
                                                    </tr>
                                                  <?php endforeach; ?>
                                                </tbody>
                                              </table>
                                            </div>
                                          </div>
                                        </div>
                                      <?php endforeach; ?>

                                      <!-- MATRIKS X -->
                                      <div class="card">
                                        <div class="card-header">
                                          <div class="card-icon" style="background:rgba(163,113,247,0.12)">🗂️</div>
                                          <div>
                                            <div class="card-title">Matriks Keputusan X</div>
                                            <div class="card-sub">Hasil fuzzifikasi × bobot kategori</div>
                                          </div>
                                        </div>
                                        <div class="card-body">
                                          <div class="tbl-wrap">
                                            <table>
                                              <thead>
                                                <tr>
                                                  <th>Kode</th>
                                                  <th>C1<br>Kedisiplinan</th>
                                                  <th>C2<br>Penelitian</th>
                                                  <th>C3<br>Penilaian</th>
                                                  <th>C4<br>Masa Kerja</th>
                                                  <th>C5<br>Prestasi</th>
                                                  <th>C6<br>Sertifikasi</th>
                                                  <th>C7<br>Pengabdian</th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                <?php foreach ($fuzz as $d): ?>
                                                  <tr>
                                                    <td><span class="badge badge-blue mono"><?= $d['kode'] ?></span></td>
                                                    <?php foreach ($d['X'] as $j => $v): ?>
                                                      <td class="mono" style="color:<?= $j < 4 ? 'var(--accent2)' : 'var(--muted)' ?>"><?= fmt($v) ?></td>
                                                    <?php endforeach; ?>
                                                  </tr>
                                                <?php endforeach; ?>
                                              </tbody>
                                            </table>
                                          </div>
                                          <div class="btn-row">
                                            <a href="normalisasi.php" class="btn btn-primary">Lanjut → Normalisasi</a>
                                            <a href="index.php" class="btn btn-secondary">← Kembali</a>
                                          </div>
                                        </div>
                                      </div>

                                  </div>
                                  <?php require_once 'includes/footer.php'; ?>