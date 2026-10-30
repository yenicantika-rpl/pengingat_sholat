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

$nama_admin = $_SESSION['nama'] ?? 'Administrator';


/* =========================
   HAPUS JADWAL
========================= */

if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    mysqli_query(
        $koneksi,
        "DELETE FROM jadwal_sholat WHERE id = $id"
    );

    header("Location: jadwal.php");
    exit;
}


/* =========================
   AMBIL DATA JADWAL
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT 
        jadwal_sholat.*,
        users.nama,
        users.email
     FROM jadwal_sholat
     LEFT JOIN users
        ON jadwal_sholat.user_id = users.id
     ORDER BY jadwal_sholat.tanggal DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Jadwal Sholat | SholatKu</title>


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
   MENU LIST
===================================== */

.menu-list {
    list-style: none;

    padding: 0;

    margin: 0;
}


.menu-list li {
    list-style: none;
}


/* =====================================
   MENU
===================================== */

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


/* =====================================
   LOGOUT
===================================== */

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


/* =====================================
   PANEL HEADER
===================================== */

.panel-header {
    padding: 25px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    border-bottom: 1px solid #eef2f1;
}


.panel-title h2 {
    font-size: 18px;

    color: #123b36;

    margin-bottom: 5px;
}


.panel-title p {
    color: #94a3b8;

    font-size: 11px;
}


/* =====================================
   BUTTON TAMBAH
===================================== */

.btn-tambah {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    background: #047857;

    color: white;

    text-decoration: none;

    padding: 11px 17px;

    border-radius: 10px;

    font-size: 12px;

    font-weight: bold;

    transition: .25s;
}


.btn-tambah:hover {
    background: #065f46;

    transform: translateY(-2px);
}


/* =====================================
   TABLE WRAPPER
===================================== */

.table-wrapper {
    width: 100%;

    overflow-x: auto;
}


/* =====================================
   TABLE
===================================== */

table {
    width: 100%;

    border-collapse: collapse;

    min-width: 900px;
}


thead {
    background: #f0fdf4;
}


thead th {
    padding: 15px 18px;

    text-align: left;

    color: #166534;

    font-size: 11px;

    font-weight: bold;

    border-bottom: 1px solid #dcfce7;

    white-space: nowrap;
}


tbody td {
    padding: 15px 18px;

    font-size: 12px;

    color: #475569;

    border-bottom: 1px solid #f1f5f9;

    white-space: nowrap;
}


tbody tr:hover {
    background: #f8fafc;
}


tbody tr:last-child td {
    border-bottom: none;
}


/* =====================================
   USER
===================================== */

.user-name {
    color: #1f2937;

    font-weight: bold;

    margin-bottom: 3px;
}


.user-email {
    color: #94a3b8;

    font-size: 10px;
}


/* =====================================
   JAM SHOLAT
===================================== */

.jam {
    color: #047857;

    font-weight: bold;
}


/* =====================================
   ACTION
===================================== */

.action {
    display: flex;

    gap: 7px;
}


.btn-edit,
.btn-hapus {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    width: 34px;
    height: 34px;

    border-radius: 8px;

    text-decoration: none;

    font-size: 14px;

    transition: .2s;
}


.btn-edit {
    background: #ecfdf5;

    color: #047857;
}


.btn-edit:hover {
    background: #d1fae5;
}


.btn-hapus {
    background: #fef2f2;

    color: #dc2626;
}


.btn-hapus:hover {
    background: #fee2e2;
}


/* =====================================
   EMPTY
===================================== */

.empty {
    text-align: center;

    padding: 60px 20px;

    color: #94a3b8;
}


.empty-icon {
    font-size: 40px;

    margin-bottom: 12px;
}


.empty h3 {
    color: #475569;

    font-size: 15px;

    margin-bottom: 6px;
}


.empty p {
    font-size: 12px;
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


    .header {
        padding: 20px;
    }


    .admin-box {
        display: none;
    }


    .panel-header {
        align-items: flex-start;

        gap: 15px;
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
               class="menu">

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
               class="menu active">

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
                MENU UTAMA
            </div>

            <h1>
                Jadwal Sholat
            </h1>

            <p>
                Kelola seluruh jadwal sholat pengguna.
            </p>

        </div>


        <!-- ADMIN -->

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
         PANEL JADWAL
    ====================================== -->

    <div class="panel">


        <div class="panel-header">

            <div class="panel-title">

                <h2>
                    Data Jadwal Sholat
                </h2>

                <p>
                    Daftar jadwal sholat seluruh pengguna.
                </p>

            </div>


            <a
                href="tambah.php"
                class="btn-tambah"
            >

                ➕

                Tambah Jadwal

            </a>

        </div>



        <?php if (
            mysqli_num_rows($query) > 0
        ): ?>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                NO
                            </th>

                            <th>
                                PENGGUNA
                            </th>

                            <th>
                                TANGGAL
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

                            <th>
                                AKSI
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $no = 1;

                    while (
                        $row = mysqli_fetch_assoc($query)
                    ):

                    ?>


                        <tr>


                            <!-- NO -->

                            <td>

                                <?= $no++; ?>

                            </td>



                            <!-- USER -->

                            <td>

                                <div class="user-name">

                                    <?= htmlspecialchars(
                                        $row['nama']
                                        ?? 'User'
                                    ); ?>

                                </div>

                                <div class="user-email">

                                    <?= htmlspecialchars(
                                        $row['email']
                                        ?? '-'
                                    ); ?>

                                </div>

                            </td>



                            <!-- TANGGAL -->

                            <td>

                                <?= date(
                                    'd-m-Y',
                                    strtotime(
                                        $row['tanggal']
                                    )
                                ); ?>

                            </td>



                            <!-- SUBUH -->

                            <td>

                                <span class="jam">

                                    <?= htmlspecialchars(
                                        $row['subuh']
                                    ); ?>

                                </span>

                            </td>



                            <!-- DZUHUR -->

                            <td>

                                <span class="jam">

                                    <?= htmlspecialchars(
                                        $row['dzuhur']
                                    ); ?>

                                </span>

                            </td>



                            <!-- ASHAR -->

                            <td>

                                <span class="jam">

                                    <?= htmlspecialchars(
                                        $row['ashar']
                                    ); ?>

                                </span>

                            </td>



                            <!-- MAGHRIB -->

                            <td>

                                <span class="jam">

                                    <?= htmlspecialchars(
                                        $row['maghrib']
                                    ); ?>

                                </span>

                            </td>



                            <!-- ISYA -->

                            <td>

                                <span class="jam">

                                    <?= htmlspecialchars(
                                        $row['isya']
                                    ); ?>

                                </span>

                            </td>



                            <!-- AKSI -->

                            <td>

                                <div class="action">


                                    <!-- EDIT -->

                                    <a
                                        href="edit.php?id=<?= $row['id']; ?>"
                                        class="btn-edit"
                                        title="Edit"
                                    >
                                        ✏️
                                    </a>


                                    <!-- HAPUS -->

                                    <a
                                        href="jadwal.php?hapus=<?= $row['id']; ?>"
                                        class="btn-hapus"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus jadwal ini?');"
                                    >
                                        🗑️
                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="empty">

                <div class="empty-icon">
                    🕌
                </div>

                <h3>
                    Belum Ada Jadwal
                </h3>

                <p>
                    Belum ada data jadwal sholat yang tersimpan.
                </p>

            </div>


        <?php endif; ?>


    </div>



    <!-- FOOTER -->

    <div class="footer">

        © <?= date('Y'); ?> SholatKu
        · Aplikasi Pengingat Sholat

    </div>


</main>


</body>

</html>