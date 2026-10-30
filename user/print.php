<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (($_SESSION['role'] ?? '') !== 'user') {
    header("Location: ../admin/dashboard.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$nama = $_SESSION['nama'] ?? 'User';

$query = mysqli_query($koneksi, "
    SELECT *
    FROM jadwal_sholat
    WHERE user_id = $user_id
    ORDER BY tanggal ASC
");

if (!$query) {
    die("Gagal mengambil jadwal: " . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Print Jadwal Sholat | SholatKu</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f1f5f9;
    color: #334155;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 250px;
    background: linear-gradient(180deg, #064e3b, #047857);
    padding: 25px 15px;
    color: white;
    box-shadow: 4px 0 15px rgba(0,0,0,.08);
    z-index: 1000;
    overflow-y: auto;
    overflow-x: hidden;
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 5px 10px 25px;
}

.logo-icon {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,.15);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 25px;
}

.logo-text h2 {
    font-size: 20px;
    color: white;
}

.logo-text span {
    display: block;
    color: #a7f3d0;
    font-size: 11px;
    margin-top: 3px;
}

.menu-title {
    font-size: 11px;
    color: #a7f3d0;
    font-weight: bold;
    padding: 15px 15px 10px;
}

.menu-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.menu-list li {
    list-style: none;
    margin-bottom: 5px;
}

.menu {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    color: #ecfdf5;
    text-decoration: none;
    padding: 13px 15px;
    border-radius: 12px;
    font-size: 14px;
    transition: .3s;
}

.menu:hover {
    background: rgba(255,255,255,.15);
    transform: translateX(4px);
}

.menu.active {
    background: white;
    color: #047857;
    font-weight: bold;
}

.menu-icon {
    width: 25px;
    min-width: 25px;
    text-align: center;
    font-size: 18px;
}

.menu.logout {
    margin-top: 15px;
    color: #fee2e2;
}

/* KONTEN */
.main {
    margin-left: 250px;
    padding: 25px;
    min-height: 100vh;
}

/* HEADER ATAS */
.header {
    background: white;
    border-radius: 20px;
    padding: 25px 30px;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(0,0,0,.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.header-label {
    color: #059669;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 1px;
}

.header h1 {
    color: #082f2b;
    font-size: 28px;
    margin: 7px 0;
}

.header p {
    color: #64748b;
    font-size: 13px;
}

.user-name {
    background: #d1fae5;
    color: #047857;
    padding: 14px 18px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: bold;
    white-space: nowrap;
}

/* KARTU LAPORAN */
.card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,.05);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 22px 28px;
    border-bottom: 1px solid #e2e8f0;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 14px;
}

.card-icon {
    width: 48px;
    height: 48px;
    background: #d1fae5;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
}

.card-title h2 {
    color: #082f2b;
    font-size: 20px;
    margin-bottom: 5px;
}

.card-title p {
    color: #64748b;
    font-size: 12px;
}

.actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 13px 18px;
    border: none;
    border-radius: 9px;
    text-decoration: none;
    font-size: 13px;
    font-weight: bold;
    cursor: pointer;
}

.btn-back {
    background: #e2e8f0;
    color: #334155;
}

.btn-print {
    background: #059669;
    color: white;
}

.btn:hover {
    opacity: .88;
}

/* AREA LAPORAN */
.report {
    padding: 35px 30px 25px;
}

.report-heading {
    text-align: center;
    margin-bottom: 25px;
}

.mosque {
    font-size: 38px;
    margin-bottom: 8px;
}

.report-heading h2 {
    color: #064e3b;
    font-size: 23px;
    margin-bottom: 7px;
}

.report-heading p {
    color: #64748b;
    font-size: 12px;
}

/* TABEL */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 700px;
    border-collapse: collapse;
    font-size: 12px;
}

thead {
    background: #064e3b;
    color: white;
}

thead th {
    padding: 14px 10px;
    text-align: center;
    white-space: nowrap;
}

tbody td {
    padding: 13px 10px;
    text-align: center;
    border: 1px solid #dbe4ee;
    color: #334155;
}

tbody tr:nth-child(even) {
    background: #f8fafc;
}

.empty {
    text-align: center;
    padding: 25px;
    color: #64748b;
}

.footer {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 28px;
    padding-top: 15px;
    border-top: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 11px;
}

/* RESPONSIVE */
@media (max-width: 800px) {
    .main {
        padding: 18px;
    }

    .header {
        padding: 22px;
    }

    .card-header {
        align-items: flex-start;
    }
}

@media (max-width: 600px) {
    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        bottom: auto;
    }

    .main {
        margin-left: 0;
        padding: 15px;
    }

    .header,
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header h1 {
        font-size: 24px;
    }

    .actions {
        width: 100%;
    }

    .report {
        padding: 25px 15px;
    }

    .footer {
        flex-direction: column;
    }
}

/* KHUSUS CETAK */
@media print {
    body {
        background: white;
    }

    .sidebar,
    .header,
    .actions {
        display: none !important;
    }

    .main {
        margin: 0;
        padding: 0;
    }

    .card {
        box-shadow: none;
        border-radius: 0;
    }

    .card-header {
        padding: 10px 0;
        border-bottom: none;
    }

    .card-icon {
        display: none;
    }

    .report {
        padding: 15px 0;
    }

    .table-wrapper {
        overflow: visible;
    }

    table {
        min-width: 0;
        width: 100%;
        font-size: 10px;
    }

    thead {
        background: #064e3b !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    th, td {
        padding: 8px 5px;
        border: 1px solid #999;
    }

    .footer {
        margin-top: 20px;
    }

    @page {
        size: landscape;
        margin: 12mm;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR USER -->
<aside class="sidebar">
    <div class="logo">
        <div class="logo-icon">🕌</div>
        <div class="logo-text">
            <h2>SholatKu</h2>
            <span>Pengingat Sholat</span>
        </div>
    </div>

    <div class="menu-title">MENU UTAMA</div>
    <ul class="menu-list">
        <li><a href="dashboard.php" class="menu">
            <span class="menu-icon">🏠</span><span>Dashboard</span>
        </a></li>
        <li><a href="jadwal.php" class="menu">
            <span class="menu-icon">🕌</span><span>Jadwal Sholat</span>
        </a></li>
        <li><a href="tambah.php" class="menu">
            <span class="menu-icon">➕</span><span>Tambah Jadwal</span>
        </a></li>
        <li><a href="kiblat.php" class="menu">
            <span class="menu-icon">🧭</span><span>Arah Kiblat</span>
        </a></li>
    </ul>

    <div class="menu-title">AKUN</div>
    <ul class="menu-list">
        <li><a href="profil.php" class="menu">
            <span class="menu-icon">👤</span><span>Profil</span>
        </a></li>
        <li><a href="pengaturan.php" class="menu">
            <span class="menu-icon">⚙️</span><span>Pengaturan Akun</span>
        </a></li>
    </ul>

    <div class="menu-title">LAPORAN</div>
    <ul class="menu-list">
        <li><a href="print.php" class="menu active">
            <span class="menu-icon">🖨️</span><span>Print Jadwal</span>
        </a></li>
    </ul>

    <ul class="menu-list">
        <li><a href="../logout.php" class="menu logout">
            <span class="menu-icon">🚪</span><span>Logout</span>
        </a></li>
    </ul>
</aside>

<!-- KONTEN UTAMA -->
<main class="main">

    <div class="header">
        <div>
            <div class="header-label">LAPORAN</div>
            <h1>Print Jadwal Sholat</h1>
            <p>Cetak laporan jadwal sholat Anda.</p>
        </div>

        <div class="user-name">
            👤 <?= htmlspecialchars($nama) ?>
        </div>
    </div>

    <section class="card">

        <div class="card-header">
            <div class="card-title">
                <div class="card-icon">🖨️</div>
                <div>
                    <h2>Laporan Jadwal Sholat</h2>
                    <p>Siap untuk dicetak atau disimpan sebagai PDF.</p>
                </div>
            </div>

            <div class="actions">
                <a href="dashboard.php" class="btn btn-back">
                    ← Kembali
                </a>
                <button onclick="window.print()" class="btn btn-print">
                    🖨️ Cetak Sekarang
                </button>
            </div>
        </div>

        <div class="report">
            <div class="report-heading">
                <div class="mosque">🕌</div>
                <h2>LAPORAN JADWAL SHOLAT</h2>
                <p>Aplikasi Pengingat Sholat — SholatKu</p>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Subuh</th>
                            <th>Dzuhur</th>
                            <th>Ashar</th>
                            <th>Maghrib</th>
                            <th>Isya</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    $no = 1;

                    if (mysqli_num_rows($query) > 0):
                        while ($data = mysqli_fetch_assoc($query)):
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <?= !empty($data['tanggal'])
                                    ? date('d-m-Y', strtotime($data['tanggal']))
                                    : '-' ?>
                            </td>
                            <td><?= htmlspecialchars($data['subuh'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($data['dzuhur'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($data['ashar'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($data['maghrib'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($data['isya'] ?? '-') ?></td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="7" class="empty">
                                Belum ada jadwal sholat untuk dicetak.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <span>Dicetak oleh: <?= htmlspecialchars($nama) ?></span>
                <span>Tanggal: <?= date('d-m-Y H:i') ?></span>
            </div>
        </div>
    </section>

</main>
</body>
</html>