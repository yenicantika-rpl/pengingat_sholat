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

$user_id = (int) $_SESSION['user_id'];

$nama_admin = $_SESSION['nama'] ?? 'Administrator';


/* =========================
   AMBIL DATA ADMIN
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT id, nama, email, role
     FROM users
     WHERE id = $user_id
     LIMIT 1"
);

$admin = mysqli_fetch_assoc($query);


/* =========================
   JIKA DATA TIDAK DITEMUKAN
========================= */

if (!$admin) {
    $admin = [
        'nama' => $nama_admin,
        'email' => '-',
        'role' => 'admin'
    ];
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Profil Admin | SholatKu</title>


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
   PROFILE CARD
===================================== */

.profile-card {
    background: white;

    border-radius: 17px;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,.05);

    overflow: hidden;
}


/* =====================================
   PROFILE HEADER
===================================== */

.profile-header {
    background: linear-gradient(
        135deg,
        #064e3b,
        #047857
    );

    padding: 35px;

    display: flex;

    align-items: center;

    gap: 20px;

    color: white;
}


.profile-avatar {
    width: 82px;
    height: 82px;

    border-radius: 50%;

    background: white;

    color: #047857;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 32px;

    font-weight: bold;

    flex-shrink: 0;
}


.profile-header h2 {
    font-size: 22px;

    margin-bottom: 7px;
}


.profile-header p {
    font-size: 12px;

    color: #a7f3d0;
}


/* =====================================
   PROFILE BODY
===================================== */

.profile-body {
    padding: 28px;
}


.profile-title {
    color: #123b36;

    font-size: 17px;

    font-weight: bold;

    margin-bottom: 20px;
}


/* =====================================
   DATA ROW
===================================== */

.profile-row {
    display: grid;

    grid-template-columns: 180px 1fr;

    border-bottom: 1px solid #eef2f1;

    padding: 17px 5px;
}


.profile-row:last-child {
    border-bottom: none;
}


.profile-label {
    color: #64748b;

    font-size: 12px;

    font-weight: bold;
}


.profile-value {
    color: #1f2937;

    font-size: 13px;

    font-weight: 500;
}


.role-badge {
    display: inline-block;

    padding: 6px 12px;

    border-radius: 20px;

    background: #fef3c7;

    color: #92400e;

    font-size: 10px;

    font-weight: bold;
}


/* =====================================
   INFO BOX
===================================== */

.info-box {
    margin-top: 25px;

    padding: 16px 18px;

    background: #f0fdf4;

    border: 1px solid #dcfce7;

    border-radius: 11px;

    color: #166534;

    font-size: 12px;

    line-height: 1.6;
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


    .profile-header {
        padding: 25px;

        flex-direction: column;

        text-align: center;
    }


    .profile-row {
        grid-template-columns: 1fr;

        gap: 7px;
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
               class="menu active">

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
                Profil Admin
            </h1>

            <p>
                Informasi akun administrator SholatKu.
            </p>

        </div>


        <!-- ADMIN -->

        <div class="admin-box">

            <div class="avatar">

                <?= strtoupper(
                    substr(
                        $admin['nama'],
                        0,
                        1
                    )
                ); ?>

            </div>


            <div>

                <strong>
                    <?= htmlspecialchars(
                        $admin['nama']
                    ); ?>
                </strong>

                <span>
                    Administrator
                </span>

            </div>

        </div>

    </div>



    <!-- =====================================
         PROFILE
    ====================================== -->

    <div class="profile-card">


        <!-- PROFILE HEADER -->

        <div class="profile-header">

            <div class="profile-avatar">

                <?= strtoupper(
                    substr(
                        $admin['nama'],
                        0,
                        1
                    )
                ); ?>

            </div>


            <div>

                <h2>

                    <?= htmlspecialchars(
                        $admin['nama']
                    ); ?>

                </h2>

                <p>
                    Administrator SholatKu
                </p>

            </div>

        </div>



        <!-- PROFILE BODY -->

        <div class="profile-body">


            <div class="profile-title">
                Informasi Akun
            </div>


            <!-- NAMA -->

            <div class="profile-row">

                <div class="profile-label">
                    Nama Lengkap
                </div>

                <div class="profile-value">

                    <?= htmlspecialchars(
                        $admin['nama']
                    ); ?>

                </div>

            </div>



            <!-- EMAIL -->

            <div class="profile-row">

                <div class="profile-label">
                    Email
                </div>

                <div class="profile-value">

                    <?= htmlspecialchars(
                        $admin['email']
                    ); ?>

                </div>

            </div>



            <!-- ROLE -->

            <div class="profile-row">

                <div class="profile-label">
                    Role
                </div>

                <div class="profile-value">

                    <span class="role-badge">
                        ADMIN
                    </span>

                </div>

            </div>



            <!-- ID -->

            <div class="profile-row">

                <div class="profile-label">
                    ID Pengguna
                </div>

                <div class="profile-value">

                    <?= htmlspecialchars(
                        $admin['id']
                    ); ?>

                </div>

            </div>



            <!-- INFO -->

            <div class="info-box">

                👤 Akun ini memiliki hak akses sebagai
                administrator untuk mengelola jadwal sholat
                dan data pengguna pada aplikasi SholatKu.

            </div>


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