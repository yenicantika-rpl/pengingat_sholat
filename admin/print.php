<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

$nama_admin = $_SESSION['nama'] ?? 'Administrator';

/* Ambil semua jadwal */
$query = mysqli_query(
    $koneksi,
    "SELECT 
        jadwal_sholat.*,
        users.nama
     FROM jadwal_sholat
     LEFT JOIN users
        ON jadwal_sholat.user_id = users.id
     ORDER BY jadwal_sholat.tanggal ASC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

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
    color: #1e293b;
}


/* =========================
   SIDEBAR
========================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;

    width: 260px;

    background: linear-gradient(
        180deg,
        #064e3b,
        #047857
    );

    padding: 25px 18px;

    color: white;
}

.logo {
    display: flex;
    align-items: center;

    gap: 12px;

    padding: 10px;

    margin-bottom: 40px;
}

.logo-icon {
    width: 48px;
    height: 48px;

    background: #10b981;

    border-radius: 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 24px;
}

.logo-text h2 {
    font-size: 20px;
}

.logo-text p {
    color: #a7f3d0;
    font-size: 11px;

    margin-top: 4px;
}

.menu-title {
    color: #7dd3c7;

    font-size: 10px;

    font-weight: bold;

    letter-spacing: 1px;

    padding: 0 12px;

    margin-bottom: 10px;
}

.menu {
    display: flex;

    align-items: center;

    gap: 13px;

    padding: 13px 14px;

    margin-bottom: 6px;

    border-radius: 10px;

    color: #d1fae5;

    text-decoration: none;

    font-size: 14px;

    transition: .2s;
}

.menu:hover {
    background: rgba(255,255,255,.08);

    color: white;
}

.menu.active {
    background: #10b981;

    color: white;

    box-shadow:
        0 7px 20px
        rgba(16,185,129,.25);
}

.menu.logout {
    margin-top: 30px;

    color: #fecaca;
}

.menu.logout:hover {
    background: #991b1b;

    color: white;
}


/* =========================
   MAIN
========================= */

.main {
    margin-left: 260px;

    padding: 30px;

    min-height: 100vh;
}


/* =========================
   HEADER
========================= */

.header {
    background: white;

    border-radius: 18px;

    padding: 25px 28px;

    margin-bottom: 25px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,.06);
}

.header-small {
    color: #10b981;

    font-size: 11px;

    font-weight: bold;

    margin-bottom: 7px;
}

.header h1 {
    color: #102a27;

    font-size: 27px;

    margin-bottom: 7px;
}

.header p {
    color: #64748b;

    font-size: 13px;
}

.admin {
    display: flex;

    align-items: center;

    gap: 12px;
}

.avatar {
    width: 48px;
    height: 48px;

    border-radius: 50%;

    background: #d1fae5;

    color: #047857;

    display: flex;

    align-items: center;

    justify-content: center;

    font-weight: bold;
}

.admin strong {
    display: block;

    font-size: 13px;
}

.admin span {
    display: block;

    color: #94a3b8;

    font-size: 11px;

    margin-top: 4px;
}


/* =========================
   PRINT CARD
========================= */

.print-card {
    background: white;

    border-radius: 18px;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,.06);

    overflow: hidden;
}

.print-header {
    padding: 22px 25px;

    border-bottom:
        1px solid #edf2f0;

    display: flex;

    justify-content: space-between;

    align-items: center;
}

.title {
    display: flex;

    align-items: center;

    gap: 13px;
}

.title-icon {
    width: 45px;
    height: 45px;

    border-radius: 12px;

    background: #d1fae5;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 22px;
}

.title h2 {
    color: #123b36;

    font-size: 18px;

    margin-bottom: 4px;
}

.title p {
    color: #94a3b8;

    font-size: 11px;
}


/* =========================
   BUTTON
========================= */

.buttons {
    display: flex;

    gap: 8px;
}

.btn {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    height: 40px;

    padding: 0 17px;

    border-radius: 8px;

    text-decoration: none;

    font-size: 12px;

    font-weight: bold;

    border: none;

    cursor: pointer;
}

.btn-print {
    background: #059669;

    color: white;
}

.btn-print:hover {
    background: #047857;
}

.btn-back {
    background: #e2e8f0;

    color: #475569;
}


/* =========================
   REPORT
========================= */

.report {
    padding: 30px;
}

.report-title {
    text-align: center;

    margin-bottom: 25px;
}

.report-title .mosque {
    font-size: 35px;

    margin-bottom: 8px;
}

.report-title h2 {
    color: #123b36;

    font-size: 22px;
}

.report-title p {
    color: #64748b;

    font-size: 12px;

    margin-top: 6px;
}


/* =========================
   TABLE
========================= */

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;

    border-collapse: collapse;

    min-width: 850px;
}

thead {
    background: #063b35;
}

th {
    color: white;

    padding: 13px 10px;

    font-size: 10px;

    text-align: center;

    border: 1px solid #0a4c44;
}

td {
    padding: 12px 10px;

    font-size: 11px;

    text-align: center;

    border: 1px solid #e2e8f0;
}

tbody tr:nth-child(even) {
    background: #f8fafc;
}

.user-name {
    text-align: left;

    font-weight: bold;

    color: #334155;
}

.empty {
    padding: 50px !important;

    color: #94a3b8;

    text-align: center;
}


/* =========================
   FOOTER
========================= */

.report-footer {
    margin-top: 30px;

    display: flex;

    justify-content: space-between;

    color: #64748b;

    font-size: 11px;

    border-top:
        1px solid #e2e8f0;

    padding-top: 15px;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:800px) {

    .sidebar {
        width: 70px;

        padding: 20px 8px;
    }

    .logo-text,
    .menu-title,
    .menu span:last-child {
        display: none;
    }

    .logo {
        justify-content: center;
    }

    .menu {
        justify-content: center;
    }

    .main {
        margin-left: 70px;

        padding: 15px;
    }

    .admin {
        display: none;
    }

    .print-header {
        flex-direction: column;

        align-items: flex-start;

        gap: 15px;
    }
}


/* =========================
   KHUSUS PRINT
========================= */

@media print {

    @page {
        size: A4 landscape;

        margin: 15mm;
    }

    body {
        background: white;
    }

    .sidebar,
    .header,
    .buttons {
        display: none !important;
    }

    .main {
        margin: 0;

        padding: 0;
    }

    .print-card {
        box-shadow: none;

        border-radius: 0;
    }

    .print-header {
        display: none;
    }

    .report {
        padding: 0;
    }

    .report-title {
        margin-bottom: 20px;
    }

    table {
        min-width: 0;

        width: 100%;
    }

    th {
        background: #063b35 !important;

        color: white !important;

        -webkit-print-color-adjust: exact;

        print-color-adjust: exact;
    }

    td {
        font-size: 10px;
    }

    .report-footer {
        margin-top: 20px;
    }
}

</style>

</head>


<body>


<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="logo">

        <div class="logo-icon">
            🕌
        </div>

        <div class="logo-text">

            <h2>SholatKu</h2>

            <p>Pengingat Sholat</p>

        </div>

    </div>


    <div class="menu-title">
        ADMIN PANEL
    </div>


    <a href="dashboard.php"
       class="menu">

        <span>🏠</span>

        <span>Dashboard</span>

    </a>


    <a href="jadwal.php"
       class="menu">

        <span>🕐</span>

        <span>Jadwal Sholat</span>

    </a>


    <a href="tambah.php"
       class="menu">

        <span>➕</span>

        <span>Tambah Jadwal</span>

    </a>


    <a href="users.php"
       class="menu">

        <span>👥</span>

        <span>Data User</span>

    </a>
    <!-- AKUN -->
<div class="menu-title" style="margin-top:30px;">
    AKUN
</div>

<!-- PROFIL ADMIN -->
<a href="profil.php" class="menu">
    <span class="menu-icon">👤</span>
    <span>Profil Admin</span>
</a>

<!-- PENGATURAN -->
<a href="pengaturan.php" class="menu">
    <span class="menu-icon">⚙️</span>
    <span>Pengaturan</span>
</a>


    <div class="menu-title"
         style="margin-top:30px;">

        LAPORAN

    </div>


    <a href="print.php"
       class="menu active">

        <span>🖨️</span>

        <span>Print Jadwal</span>

    </a>


    <a href="../logout.php"
       class="menu logout">

        <span>🚪</span>

        <span>Logout</span>

    </a>

</aside>



<!-- MAIN -->

<main class="main">


    <!-- HEADER -->

    <div class="header">

        <div>

            <div class="header-small">
                LAPORAN
            </div>

            <h1>
                Print Jadwal Sholat
            </h1>

            <p>
                Cetak laporan jadwal sholat pengguna.
            </p>

        </div>


        <div class="admin">

            <div class="avatar">

                <?= strtoupper(
                    substr($nama_admin, 0, 1)
                ); ?>

            </div>

            <div>

                <strong>
                    <?= htmlspecialchars($nama_admin); ?>
                </strong>

                <span>
                    Administrator
                </span>

            </div>

        </div>

    </div>



    <!-- PRINT CARD -->

    <div class="print-card">


        <div class="print-header">

            <div class="title">

                <div class="title-icon">
                    🖨️
                </div>

                <div>

                    <h2>
                        Laporan Jadwal Sholat
                    </h2>

                    <p>
                        Siap untuk dicetak atau disimpan sebagai PDF.
                    </p>

                </div>

            </div>


            <div class="buttons">

                <a
                    href="jadwal.php"
                    class="btn btn-back"
                >
                    ← Kembali
                </a>

                <button
                    onclick="window.print()"
                    class="btn btn-print"
                >
                    🖨️ Cetak Sekarang
                </button>

            </div>

        </div>



        <!-- REPORT -->

        <div class="report">


            <div class="report-title">

                <div class="mosque">
                    🕌
                </div>

                <h2>
                    LAPORAN JADWAL SHOLAT
                </h2>

                <p>
                    Aplikasi Pengingat Sholat — SholatKu
                </p>

            </div>



            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                NO
                            </th>

                            <th>
                                TANGGAL
                            </th>

                            <th>
                                PENGGUNA
                            </th>

                            <th>
                                SUBUH
                            </th>

                            <th>
                                DZUHUR
                            </th>

                            <th>
                                ASHAR
                            </th>

                            <th>
                                MAGHRIB
                            </th>

                            <th>
                                ISYA
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php

                    $no = 1;

                    if (
                        mysqli_num_rows($query) > 0
                    ):

                        while (
                            $row =
                            mysqli_fetch_assoc($query)
                        ):

                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>

                            <td>
                                <?= date(
                                    'd-m-Y',
                                    strtotime(
                                        $row['tanggal']
                                    )
                                ); ?>
                            </td>

                            <td class="user-name">

                                <?= htmlspecialchars(
                                    $row['nama'] ?? 'User'
                                ); ?>

                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['subuh']
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['dzuhur']
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['ashar']
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['maghrib']
                                ); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['isya']
                                ); ?>
                            </td>

                        </tr>

                    <?php

                        endwhile;

                    else:

                    ?>

                        <tr>

                            <td
                                colspan="8"
                                class="empty"
                            >

                                📭

                                <br><br>

                                Belum ada data jadwal sholat.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>



            <div class="report-footer">

                <span>
                    Dicetak oleh:
                    <?= htmlspecialchars($nama_admin); ?>
                </span>

                <span>
                    Tanggal:
                    <?= date('d-m-Y H:i'); ?>
                </span>

            </div>


        </div>

    </div>


</main>


</body>

</html>