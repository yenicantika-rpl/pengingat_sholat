<?php

session_start();
include "../koneksi.php";

/* =========================
   CEK LOGIN
========================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}


/* =========================
   DATA ADMIN
========================= */

$nama_admin = $_SESSION['nama'] ?? 'Administrator';


/* =========================
   TOTAL USER
========================= */

$query_user = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role='user'"
);

$total_user = mysqli_fetch_assoc($query_user)['total'];


/* =========================
   TOTAL JADWAL
========================= */

$query_jadwal = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total
     FROM jadwal_sholat"
);

$total_jadwal = mysqli_fetch_assoc($query_jadwal)['total'];


/* =========================
   DATA JADWAL TERBARU
========================= */

$data_jadwal = mysqli_query(
    $koneksi,
    "SELECT
        jadwal_sholat.*,
        users.nama
     FROM jadwal_sholat
     LEFT JOIN users
        ON jadwal_sholat.user_id = users.id
     ORDER BY jadwal_sholat.tanggal DESC
     LIMIT 10"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin | SholatKu</title>


<style>

/* =====================================
   RESET
===================================== */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/* =====================================
   BODY
===================================== */

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f7f6;
    color: #1f2937;
}


/* =====================================
   SIDEBAR
===================================== */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;

    width: 250px;

    background: linear-gradient(
        180deg,
        #064e3b,
        #047857
    );

    padding: 25px 18px;

    color: white;

    overflow-y: auto;

    z-index: 1000;
}


/* =====================================
   LOGO
===================================== */

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

    background: #047857;

    border-radius: 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 25px;
}


.logo-text h2 {
    font-family: Arial, Helvetica, sans-serif;

    font-size: 20px;

    font-weight: bold;

    color: white;
}


.logo-text p {
    font-family: Arial, Helvetica, sans-serif;

    color: #a7f3d0;

    font-size: 10px;

    font-weight: normal;

    margin-top: 3px;
}


/* =====================================
   MENU TITLE
===================================== */

.menu-title {
    color: #a7f3d0;

    font-size: 10px;

    font-weight: bold;

    letter-spacing: 1px;

    padding: 0 12px;

    margin-bottom: 10px;
}


.section-space {
    margin-top: 30px;
}


/* =====================================
   MENU
===================================== */

.menu-list {
    list-style: none;

    padding: 0;

    margin: 0;
}


.menu-list li {
    list-style: none;
}


.menu {
    display: flex;

    align-items: center;

    gap: 13px;

    width: 100%;

    color: #d1fae5;

    text-decoration: none;

    padding: 13px 15px;

    margin-bottom: 6px;

    border-radius: 12px;

    font-size: 14px;

    font-weight: 500;

    transition: 0.3s;
}


.menu:hover {
    background: rgba(255,255,255,0.15);

    transform: translateX(4px);
}


.menu.active {
    background: white;

    color: #047857;

    font-weight: bold;

    transform: none;
}


.menu-icon {
    width: 25px;

    min-width: 25px;

    text-align: center;

    font-size: 18px;
}


.menu.active .menu-icon {
    color: #047857;
}


.menu.logout {
    margin-top: 25px;

    color: #fecaca;
}


.menu.logout:hover {
    background: rgba(239,68,68,0.15);

    color: #fecaca;

    transform: translateX(4px);
}


/* =====================================
   MAIN
===================================== */

.main {
    margin-left: 250px;

    min-height: 100vh;

    padding: 30px;
}


/* =====================================
   HEADER
===================================== */

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

    letter-spacing: .5px;
}


.header h1 {
    font-size: 28px;

    color: #102a27;

    margin-bottom: 7px;
}


.header p {
    color: #64748b;

    font-size: 14px;
}


/* =====================================
   ADMIN BOX
===================================== */

.admin-box {
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

    justify-content: center;

    align-items: center;

    font-size: 18px;

    font-weight: bold;
}


.admin-box strong {
    display: block;

    color: #1f2937;

    font-size: 13px;
}


.admin-box span {
    color: #94a3b8;

    font-size: 11px;
}


/* =====================================
   STATISTICS
===================================== */

.stats {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;

    margin-bottom: 25px;
}


.card {
    background: white;

    border-radius: 16px;

    padding: 22px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,.05);

    border: 1px solid #edf2f0;

    transition: .25s;
}


.card:hover {
    transform: translateY(-4px);

    box-shadow:
        0 12px 30px
        rgba(15,23,42,.10);
}


.card-label {
    color: #64748b;

    font-size: 11px;

    font-weight: bold;

    text-transform: uppercase;
}


.card-number {
    font-size: 30px;

    font-weight: bold;

    color: #123b36;

    margin: 7px 0;
}


.card-desc {
    color: #94a3b8;

    font-size: 11px;
}


.card-icon {
    width: 52px;
    height: 52px;

    border-radius: 14px;

    display: flex;

    justify-content: center;

    align-items: center;

    font-size: 23px;
}


.icon-user {
    background: #dbeafe;
}


.icon-jadwal {
    background: #d1fae5;
}


.icon-status {
    background: #dcfce7;
}


/* =====================================
   PANEL
===================================== */

.panel {
    background: white;

    border-radius: 17px;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,.05);

    overflow: hidden;
}


.panel-header {
    padding: 22px 25px;

    border-bottom:
        1px solid #eef2f1;

    display: flex;

    justify-content: space-between;

    align-items: center;
}


.panel-header h2 {
    font-size: 17px;

    color: #123b36;

    margin-bottom: 5px;
}


.panel-header p {
    color: #94a3b8;

    font-size: 11px;
}


.see-all {
    text-decoration: none;

    color: #059669;

    font-size: 12px;

    font-weight: bold;
}


.see-all:hover {
    color: #047857;
}


/* =====================================
   TABLE
===================================== */

.table-box {
    overflow-x: auto;
}


table {
    width: 100%;

    border-collapse: collapse;

    min-width: 700px;
}


thead {
    background: #f8faf9;
}


th {
    padding: 14px;

    text-align: left;

    font-size: 10px;

    color: #64748b;

    border-bottom:
        1px solid #e5ebe9;
}


td {
    padding: 15px 14px;

    font-size: 12px;

    color: #475569;

    border-bottom:
        1px solid #f0f3f2;
}


tbody tr:hover {
    background: #f8fcfa;
}


/* USER */

.user {
    display: flex;

    align-items: center;

    gap: 8px;
}


.user-avatar {
    width: 30px;
    height: 30px;

    background: #d1fae5;

    color: #047857;

    border-radius: 50%;

    display: flex;

    justify-content: center;

    align-items: center;

    font-weight: bold;

    font-size: 11px;
}


.empty {
    text-align: center;

    padding: 45px !important;

    color: #94a3b8;
}


/* =====================================
   FOOTER
===================================== */

.footer {
    margin-top: 25px;

    text-align: center;

    color: #94a3b8;

    font-size: 11px;
}


/* =====================================
   RESPONSIVE
===================================== */

@media(max-width: 900px) {

    .stats {
        grid-template-columns:
            repeat(2, 1fr);
    }

}


@media(max-width: 700px) {

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


    .stats {
        grid-template-columns: 1fr;
    }


    .header {
        padding: 20px;
    }


    .admin-box {
        display: none;
    }

}

</style>

</head>


<body>


<!-- =====================================
     SIDEBAR
===================================== -->

<aside class="sidebar">


    <!-- LOGO -->

    <div class="logo">

        <div class="logo-icon">
            🕌
        </div>

        <div class="logo-text">

            <h2>SholatKu</h2>

            <p>Pengingat Sholat</p>

        </div>

    </div>


    <!-- MENU UTAMA -->

    <div class="menu-title">
        MENU UTAMA
    </div>


    <ul class="menu-list">

        <li>

            <a href="dashboard.php"
               class="menu active">

                <span class="menu-icon">
                    🏠
                </span>

                <span>
                    Dashboard
                </span>

            </a>

        </li>


        <li>

            <a href="jadwal.php"
               class="menu">

                <span class="menu-icon">
                    🕌
                </span>

                <span>
                    Jadwal Sholat
                </span>

            </a>

        </li>


        <li>

            <a href="tambah.php"
               class="menu">

                <span class="menu-icon">
                    ➕
                </span>

                <span>
                    Tambah Jadwal
                </span>

            </a>

        </li>


        <li>

            <a href="users.php"
               class="menu">

                <span class="menu-icon">
                    👥
                </span>

                <span>
                    Data User
                </span>

            </a>

        </li>

    </ul>


    <!-- AKUN -->

    <div class="menu-title section-space">
        AKUN
    </div>


    <ul class="menu-list">

        <li>

            <a href="profil.php"
               class="menu">

                <span class="menu-icon">
                    👤
                </span>

                <span>
                    Profil Admin
                </span>

            </a>

        </li>


        <li>

            <a href="pengaturan.php"
               class="menu">

                <span class="menu-icon">
                    ⚙️
                </span>

                <span>
                    Pengaturan
                </span>

            </a>

        </li>

    </ul>


    <!-- LAPORAN -->

    <div class="menu-title section-space">
        LAPORAN
    </div>


    <ul class="menu-list">

        <li>

            <a href="print.php"
               class="menu">

                <span class="menu-icon">
                    🖨️
                </span>

                <span>
                    Print Jadwal
                </span>

            </a>

        </li>

    </ul>


    <!-- LOGOUT -->

    <ul class="menu-list">

        <li>

            <a href="../logout.php"
               class="menu logout">

                <span class="menu-icon">
                    🚪
                </span>

                <span>
                    Logout
                </span>

            </a>

        </li>

    </ul>


</aside>



<!-- =====================================
     MAIN
===================================== -->

<main class="main">


    <!-- HEADER -->

    <div class="header">

        <div>

            <div class="header-small">
                ADMIN PANEL
            </div>

            <h1>
                Dashboard Admin
            </h1>

            <p>
                Selamat datang kembali,
                <?= htmlspecialchars($nama_admin); ?> 👋
            </p>

        </div>


        <!-- PROFIL ADMIN -->

        <div class="admin-box">

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



    <!-- =====================================
         STATISTICS
    ====================================== -->

    <div class="stats">


        <!-- TOTAL USER -->

        <div class="card">

            <div>

                <div class="card-label">
                    Total Pengguna
                </div>

                <div class="card-number">
                    <?= $total_user; ?>
                </div>

                <div class="card-desc">
                    Pengguna terdaftar
                </div>

            </div>

            <div class="card-icon icon-user">
                👥
            </div>

        </div>


        <!-- TOTAL JADWAL -->

        <div class="card">

            <div>

                <div class="card-label">
                    Total Jadwal
                </div>

                <div class="card-number">
                    <?= $total_jadwal; ?>
                </div>

                <div class="card-desc">
                    Jadwal tersimpan
                </div>

            </div>

            <div class="card-icon icon-jadwal">
                🕐
            </div>

        </div>


        <!-- STATUS -->

        <div class="card">

            <div>

                <div class="card-label">
                    Status Sistem
                </div>

                <div
                    class="card-number"
                    style="
                    color:#16a34a;
                    font-size:22px;
                    "
                >
                    Aktif
                </div>

                <div class="card-desc">
                    Sistem berjalan normal
                </div>

            </div>

            <div class="card-icon icon-status">
                ✓
            </div>

        </div>


    </div>



    <!-- =====================================
         JADWAL TERBARU
    ====================================== -->

    <div class="panel">


        <div class="panel-header">

            <div>

                <h2>
                    Jadwal Sholat Terbaru
                </h2>

                <p>
                    Data jadwal yang tersimpan
                </p>

            </div>


            <a
                href="jadwal.php"
                class="see-all"
            >
                Lihat Semua →
            </a>

        </div>



        <div class="table-box">

            <table>

                <thead>

                    <tr>

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


                <?php if (
                    mysqli_num_rows($data_jadwal) > 0
                ): ?>


                    <?php while (
                        $row =
                        mysqli_fetch_assoc($data_jadwal)
                    ): ?>


                    <tr>


                        <td>

                            <?= date(
                                'd-m-Y',
                                strtotime(
                                    $row['tanggal']
                                )
                            ); ?>

                        </td>


                        <td>

                            <div class="user">

                                <div class="user-avatar">

                                    <?= strtoupper(
                                        substr(
                                            $row['nama'] ?? 'U',
                                            0,
                                            1
                                        )
                                    ); ?>

                                </div>

                                <?= htmlspecialchars(
                                    $row['nama'] ?? 'User'
                                ); ?>

                            </div>

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


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="7"
                            class="empty"
                        >

                            🕌

                            <br><br>

                            Belum ada jadwal sholat.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>


    </div>



    <!-- FOOTER -->

    <div class="footer">

        © <?= date('Y'); ?> SholatKu
        · Aplikasi Pengingat Sholat

    </div>


</main>


</body>

</html>