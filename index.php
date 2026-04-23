<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/fuzzy.php';
requireLogin();
$pageTitle = 'Input Data';

$db = getDB();

// Ambil data dosen dari database
function getDosenFromDB(): array {
  return getDB()->query("
      SELECT * FROM dosen 
      WHERE aktif=1 
      ORDER BY CAST(SUBSTRING(kode, 2) AS UNSIGNED)
  ")->fetchAll();
}

/* ============================================================
   CRUD DOSEN
============================================================ */

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_dosen') {
    $kode = strtoupper(trim($_POST['kode'] ?? ''));
    $nama = trim($_POST['nama'] ?? '');

    $c1 = (float)($_POST['c1'] ?? 0);
    $c2 = (float)($_POST['c2'] ?? 0);
    $c3 = (float)($_POST['c3'] ?? 0);
    $c4 = (float)($_POST['c4'] ?? 0);
    $c5 = (float)($_POST['c5'] ?? 100);
    $c6 = (float)($_POST['c6'] ?? 100);
    $c7 = (float)($_POST['c7'] ?? 100);

    if (!$kode || !$nama) {
        flash('warn', 'Kode dan Nama wajib diisi.');
        redirect('index.php');
    }

    // max 50 dosen
    $count = $db->query("SELECT COUNT(*) FROM dosen")->fetchColumn();
    if ($count >= 50) {
        flash('warn', 'Maksimal 50 dosen. Tidak bisa menambah lagi.');
        redirect('index.php');
    }

    // cek kode duplikat
    $chk = $db->prepare("SELECT id FROM dosen WHERE kode=?");
    $chk->execute([$kode]);
    if ($chk->fetch()) {
        flash('warn', "Kode dosen $kode sudah ada.");
        redirect('index.php');
    }

    $st = $db->prepare("INSERT INTO dosen (kode,nama,c1,c2,c3,c4,c5,c6,c7,aktif) VALUES (?,?,?,?,?,?,?,?,?,1)");
    $st->execute([$kode,$nama,$c1,$c2,$c3,$c4,$c5,$c6,$c7]);

    unset($_SESSION['hasil']);
    flash('success', "Dosen <b>$kode</b> berhasil ditambahkan.");
    redirect('index.php');
}


// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_dosen') {
    $id   = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');

    $c1 = (float)($_POST['c1'] ?? 0);
    $c2 = (float)($_POST['c2'] ?? 0);
    $c3 = (float)($_POST['c3'] ?? 0);
    $c4 = (float)($_POST['c4'] ?? 0);
    $c5 = (float)($_POST['c5'] ?? 100);
    $c6 = (float)($_POST['c6'] ?? 100);
    $c7 = (float)($_POST['c7'] ?? 100);

    if (!$id || !$nama) {
        flash('warn', 'Data edit tidak valid.');
        redirect('index.php');
    }

    $st = $db->prepare("UPDATE dosen SET nama=?,c1=?,c2=?,c3=?,c4=?,c5=?,c6=?,c7=? WHERE id=?");
    $st->execute([$nama,$c1,$c2,$c3,$c4,$c5,$c6,$c7,$id]);

    unset($_SESSION['hasil']);
    flash('success', "Data dosen berhasil diupdate.");
    redirect('index.php');
}


// DELETE
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id) {
        $db->prepare("DELETE FROM dosen WHERE id=?")->execute([$id]);
        unset($_SESSION['hasil']);
        flash('success', 'Data dosen berhasil dihapus.');
    }
    redirect('index.php');
}


/* ============================================================
   HITUNG FUZZY SAW
============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hitung'])) {
    $rows = getDosenFromDB();

    if (count($rows) >= 2) {
        $_SESSION['inputData'] = $rows;
        $_SESSION['hasil']     = hitungFuzzySAW($rows);

        // Simpan riwayat ke DB
        $ranked = $_SESSION['hasil']['ranked'];
        $win    = $ranked[0];
        $uid    = $_SESSION['user_id'];
        $detail = json_encode($_SESSION['hasil']['ranked']);

        $st = getDB()->prepare("INSERT INTO hasil_perhitungan 
        (user_id,jumlah_dosen,pemenang_kode,pemenang_nama,pemenang_vi,detail_json) 
        VALUES (?,?,?,?,?,?)");

        $st->execute([$uid, count($rows), $win['kode'], $win['nama'], $win['Vi'], $detail]);

        $target = isset($_POST['langsung']) ? 'hasil.php' : 'fuzzifikasi.php';
        redirect($target);
    } else {
        flash('warn', 'Minimal 2 dosen diperlukan untuk perhitungan.');
    }
}


$rows   = getDosenFromDB();
$jumlah = count($rows);

require_once 'includes/header.php';
?>

<div class="page-hero">
  <div class="hero-tag">🔬 Sistem Pendukung Keputusan</div>
  <h1>Pemilihan Dosen Terbaik<br>Metode Fuzzy SAW</h1>
  <p>Data dosen diambil dari database. CRUD dosen dapat dilakukan langsung dari halaman ini.</p>
</div>

<!-- Stats -->
<div class="stats-bar">
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(240,136,62,0.12)">👨‍🏫</div>
    <div>
      <div class="stat-val" style="color:var(--accent)"><?=$jumlah?></div>
      <div class="stat-lbl">Total Dosen</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(88,166,255,0.12)">📐</div>
    <div>
      <div class="stat-val" style="color:var(--accent2)">7</div>
      <div class="stat-lbl">Kriteria</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(163,113,247,0.12)">⚙️</div>
    <div>
      <div class="stat-val" style="color:var(--purple)">4</div>
      <div class="stat-lbl">Tahap</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(63,185,80,0.12)">✅</div>
    <div>
      <div class="stat-val" style="color:var(--accent3)">Benefit</div>
      <div class="stat-lbl">Semua Atribut</div>
    </div>
  </div>
</div>

<?php if ($m = flash('warn')): ?>
<div class="alert alert-warning alert-auto">⚠️ <?=e($m)?></div>
<?php endif; ?>

<?php if ($m = flash('success')): ?>
<div class="alert alert-success alert-auto">✅ <?=($m)?></div>
<?php endif; ?>

<?php if (isset($_SESSION['hasil'])): ?>
<div class="alert alert-success alert-auto">✓ Data sudah dihitung.
  <a href="hasil.php" style="color:var(--accent3);font-weight:600;margin-left:6px">Lihat hasil ranking →</a>
</div>
<?php endif; ?>

<!-- BOBOT -->
<div class="card">
      <div class="card-header">
        <div class="card-icon" style="background:rgba(163,113,247,0.12)">⚖️</div>
        <div>
          <div class="card-title">Bobot Kriteria</div>
          <div class="card-sub">Total bobot = 1.00 — semua atribut Benefit</div>
        </div>
      </div>
      <div class="card-body">
        <div class="weight-grid">
          <?php
          $wlabels = ['C1 — Kedisiplinan','C2 — Penelitian','C3 — Pengabdian',
                      'C4 — Penilaian Mahasiswa','C5 — Prestasi','C6 — Sertifikasi','C7 — Masa Kerja'];
          foreach (WEIGHTS as $i => $w): ?>
          <div class="weight-item">
            <div class="weight-info">
              <div class="weight-name"><?=$wlabels[$i]?></div>
              <div class="weight-bar"><div class="weight-fill" style="width:<?=($w*100*5)?>%"></div></div>
            </div>
            <div class="weight-val"><?=number_format($w, 2)?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="info-box">
          <span class="ico">ℹ️</span>
          <div>C5 (Prestasi), C6 (Sertifikasi), C7 (Pengabdian) diisi dalam skala <strong>0–100</strong>. Nilai akan dinormalisasi menjadi 0.00–1.00 dalam perhitungan. Data default menggunakan nilai 100 sesuai contoh laporan. Nilai C2 A5 dan A9 sesuai teks fuzzifikasi laporan (334 dan 536).</div>
        </div>
      </div>
    </div>

<!-- Data Dosen dari DB -->
<div class="card">
  <div class="card-header">
    <div class="card-icon" style="background:rgba(240,136,62,0.12)">📋</div>
    <div>
      <div class="card-title">Data Alternatif Dosen</div>
      <div class="card-sub">Data dari database · <?=$jumlah?> dosen aktif</div>
    </div>
    <div class="card-actions">
      <button class="btn btn-success btn-sm" onclick="showModal('modalTambah')">➕ Tambah Dosen</button>
    </div>
  </div>

  <div class="card-body">
    <?php if ($jumlah < 1): ?>
    <div class="alert alert-warning">
      ⚠️ Belum ada data dosen. Silakan tambah dosen terlebih dahulu.
    </div>
    <?php else: ?>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>Kode</th><th>Nama Dosen</th>
            <th>C1</th><th>C2</th><th>C3</th><th>C4</th><th>C5</th><th>C6</th><th>C7</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $d): ?>
          <tr>
            <td><span class="badge badge-blue mono"><?=e($d['kode'])?></span></td>
            <td style="font-weight:500"><?=e($d['nama'])?></td>
            <td class="mono"><?=number_format($d['c1'],0)?></td>
            <td class="mono"><?=number_format($d['c2'],0)?></td>
            <td class="mono"><?=number_format($d['c3'],2)?></td>
            <td class="mono"><?=number_format($d['c4'],0)?></td>
            <td class="mono"><?=number_format($d['c5'],0)?></td>
            <td class="mono"><?=number_format($d['c6'],0)?></td>
            <td class="mono"><?=number_format($d['c7'],0)?></td>
            <td style="white-space:nowrap">
              <button class="btn btn-info btn-sm"
                onclick='openEdit(<?=json_encode($d)?>)'>✏️</button>

              <a href="index.php?hapus=<?=$d['id']?>"
                 onclick="return confirm('Yakin ingin menghapus dosen ini?')"
                 class="btn btn-danger btn-sm">🗑</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($jumlah >= 2): ?>
    <form method="POST" style="margin-top:18px">
      <div class="btn-row">
        <button type="submit" name="hitung" value="1" class="btn btn-primary">⚡ Hitung Fuzzy SAW</button>
        <button type="submit" name="langsung" value="1" class="btn btn-success">🏆 Hitung & Langsung ke Hasil</button>
      </div>
    </form>
    <?php else: ?>
    <div class="alert alert-warning" style="margin-top:12px">
      ⚠️ Minimal 2 dosen diperlukan untuk perhitungan.
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div>
</div>


<!-- MODAL TAMBAH -->
<div class="modal-overlay" id="modalTambah">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">➕ Tambah Dosen</div>
      <button class="modal-close" onclick="hideModal('modalTambah')">✕</button>
    </div>

    <form method="POST">
      <input type="hidden" name="action" value="create_dosen">

      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Kode</label>
            <input type="text" name="kode" class="form-control" required placeholder="A11">
          </div>
          <div class="form-group">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" required placeholder="Nama Dosen">
          </div>
        </div>

        <div class="form-row-4">
          <div class="form-group"><label class="form-label">C1</label><input type="number" name="c1" class="form-control" required></div>
          <div class="form-group"><label class="form-label">C2</label><input type="number" name="c2" class="form-control" required></div>
          <div class="form-group"><label class="form-label">C3</label><input type="number" step="0.01" name="c3" class="form-control" required></div>
          <div class="form-group"><label class="form-label">C4</label><input type="number" name="c4" class="form-control" required></div>
        </div>

        <div class="form-row-3">
          <div class="form-group"><label class="form-label">C5</label><input type="number" name="c5" class="form-control" value="100" required></div>
          <div class="form-group"><label class="form-label">C6</label><input type="number" name="c6" class="form-control" value="100" required></div>
          <div class="form-group"><label class="form-label">C7</label><input type="number" name="c7" class="form-control" value="100" required></div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" onclick="hideModal('modalTambah')" class="btn btn-secondary">Batal</button>
        <button type="submit" class="btn btn-success">💾 Simpan</button>
      </div>
    </form>
  </div>
</div>


<!-- MODAL EDIT -->
<div class="modal-overlay" id="modalEdit">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">✏️ Edit Dosen</div>
      <button class="modal-close" onclick="hideModal('modalEdit')">✕</button>
    </div>

    <form method="POST">
      <input type="hidden" name="action" value="update_dosen">
      <input type="hidden" name="id" id="editId">

      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Kode</label>
          <input type="text" id="editKode" class="form-control" disabled>
        </div>

        <div class="form-group">
          <label class="form-label">Nama</label>
          <input type="text" name="nama" id="editNama" class="form-control" required>
        </div>

        <div class="form-row-4">
          <div class="form-group"><label class="form-label">C1</label><input type="number" name="c1" id="editC1" class="form-control" required></div>
          <div class="form-group"><label class="form-label">C2</label><input type="number" name="c2" id="editC2" class="form-control" required></div>
          <div class="form-group"><label class="form-label">C3</label><input type="number" step="0.01" name="c3" id="editC3" class="form-control" required></div>
          <div class="form-group"><label class="form-label">C4</label><input type="number" name="c4" id="editC4" class="form-control" required></div>
        </div>

        <div class="form-row-3">
          <div class="form-group"><label class="form-label">C5</label><input type="number" name="c5" id="editC5" class="form-control" required></div>
          <div class="form-group"><label class="form-label">C6</label><input type="number" name="c6" id="editC6" class="form-control" required></div>
          <div class="form-group"><label class="form-label">C7</label><input type="number" name="c7" id="editC7" class="form-control" required></div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" onclick="hideModal('modalEdit')" class="btn btn-secondary">Batal</button>
        <button type="submit" class="btn btn-primary">💾 Update</button>
      </div>
    </form>
  </div>
</div>


<script>
function showModal(id){
  document.getElementById(id).classList.add('show');
}
function hideModal(id){
  document.getElementById(id).classList.remove('show');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', e => {
    if(e.target === o) o.classList.remove('show');
  });
});

function openEdit(d){
  document.getElementById('editId').value = d.id;
  document.getElementById('editKode').value = d.kode;
  document.getElementById('editNama').value = d.nama;
  document.getElementById('editC1').value = d.c1;
  document.getElementById('editC2').value = d.c2;
  document.getElementById('editC3').value = parseFloat(d.c3).toFixed(2);
  document.getElementById('editC4').value = d.c4;
  document.getElementById('editC5').value = d.c5;
  document.getElementById('editC6').value = d.c6;
  document.getElementById('editC7').value = d.c7;

  showModal('modalEdit');
}
</script>

<?php require_once 'includes/footer.php'; ?>