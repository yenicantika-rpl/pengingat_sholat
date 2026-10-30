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
   HAPUS USER
========================= */

if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    // Jangan sampai admin terhapus
    $cek = mysqli_query(
        $koneksi,
        "SELECT role FROM users WHERE id = $id"
    );

    $user_cek = mysqli_fetch_assoc($cek);

    if ($user_cek && $user_cek['role'] === 'user') {

        mysqli_query(
            $koneksi,
            "DELETE FROM users WHERE id = $id"
        );

    }

    header("Location: users.php");
    exit;
}


/* =========================
   AMBIL DATA USER
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT id, nama, email, role
     FROM users
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Data User | SholatKu</title>


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
   BUTTON TAMBAH USER
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

    min-width: 700px;
}


thead {
    background: #f0fdf4;
}


thead th {
    padding: 15px 20px;

    text-align: left;

    color: #166534;

    font-size: 11px;

    font-weight: bold;

    border-bottom: 1px solid #dcfce7;

    white-space: nowrap;
}


tbody td {
    padding: 16px 20px;

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

    margin-bottom: 4px;
}


.user-email {
    color: #94a3b8;

    font-size: 10px;
}


/* =====================================
   ROLE
===================================== */

.badge-user {
    display: inline-block;

    padding: 6px 10px;

    background: #ecfdf5;

    color: #047857;

    border-radius: 20px;

    font-size: 10px;

    font-weight: bold;
}


.badge-admin {
    display: inline-block;

    padding: 6px 10px;

    background: #fef3c7;

    color: #92400e;

    border-radius: 20px;

    font-size: 10px;

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
   ADMIN TIDAK BISA DIHAPUS
===================================== */

.no-action {
    color: #cbd5e1;

    font-size: 11px;
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
    width: calc(100% - 250px);
    margin-left: 250px;
    margin-top: 30px;
    padding: 20px;

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
               class="menu active">

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
                Data User
            </h1>

            <p>
                Kelola data pengguna aplikasi SholatKu.
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
         PANEL DATA USER
    ====================================== -->

    <div class="panel">


        <div class="panel-header">

            <div class="panel-title">

                <h2>
                    Daftar Pengguna
                </h2>

                <p>
                    Data seluruh pengguna yang terdaftar.
                </p>

            

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
                                NAMA
                            </th>

                            <th>
                                EMAIL
                            </th>

                            <th>
                                ROLE
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



                            <!-- NAMA -->

                            <td>

                                <div class="user-name">

                                    <?= htmlspecialchars(
                                        $row['nama']
                                    ); ?>

                                </div>

                            </td>



                            <!-- EMAIL -->

                            <td>

                                <div class="user-email">

                                    <?= htmlspecialchars(
                                        $row['email']
                                    ); ?>

                                </div>

                            </td>



                            <!-- ROLE -->

                            <td>

                                <?php if (
                                    $row['role'] === 'admin'
                                ): ?>

                                    <span class="badge-admin">
                                        ADMIN
                                    </span>

                                <?php else: ?>

                                    <span class="badge-user">
                                        USER
                                    </span>

                                <?php endif; ?>

                            </td>



                            <!-- AKSI -->

                            <td>

                                <?php if (
                                    $row['role'] === 'user'
                                ): ?>

                                    <div class="action">


                                        <a
                                            href="edit_user.php?id=<?= $row['id']; ?>"
                                            class="btn-edit"
                                            title="Edit User"
                                        >
                                            ✏️
                                        </a>


                                        <a
                                            href="users.php?hapus=<?= $row['id']; ?>"
                                            class="btn-hapus"
                                            title="Hapus User"
                                            onclick="return confirm('Yakin ingin menghapus user ini?');"
                                        >
                                            🗑️
                                        </a>


                                    </div>

                                <?php else: ?>

                                    <span class="no-action">
                                        Admin
                                    </span>

                                <?php endif; ?>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                    </tbody>

                </table>

            </div>


        <?php else: ?>


            <div class="empty">

                <div class="empty-icon">
                    👥
                </div>

                <h3>
                    Belum Ada User
                </h3>

                <p>
                    Belum ada pengguna yang terdaftar.
                </p>

            </div>


        <?php endif; ?>


    </div>



    </main>

<!-- FOOTER -->

<footer class="footer">

    © <?= date('Y'); ?> SholatKu
    · Aplikasi Pengingat Sholat

</footer>