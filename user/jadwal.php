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

if ($_SESSION['role'] !== 'user') {
    header("Location: ../admin/dashboard.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$nama = $_SESSION['nama'] ?? 'User';


/* =========================
   PENCARIAN
========================= */

$cari = trim($_GET['cari'] ?? '');

if ($cari != '') {

    $keyword = "%" . $cari . "%";

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT *
         FROM jadwal_sholat
         WHERE user_id = ?
         AND tanggal LIKE ?
         ORDER BY tanggal DESC"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "is",
        $user_id,
        $keyword
    );

    mysqli_stmt_execute($stmt);

    $data = mysqli_stmt_get_result($stmt);

} else {

    $data = mysqli_query(
        $koneksi,
        "SELECT *
         FROM jadwal_sholat
         WHERE user_id = $user_id
         ORDER BY tanggal DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Jadwal Sholat | SholatKu</title>

<style>

/* =========================
   RESET
========================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/* =========================
   BODY
========================= */

body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #1f2937;
}



.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    width: 250px;
    background: linear-gradient(180deg, #064e3b, #047857);
    padding: 25px 18px;
    color: white;
    overflow-y: auto;
    z-index: 1000;
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
    background: #047857;
    color: white;
    border-radius: 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 25px;
}

.logo-text h2 {
    margin: 0;
    font-size: 20px;
    font-weight: bold;
    color: white;
}

.logo-text span {
    display: block;
    color: #a7f3d0;
    font-size: 11px;
    margin-top: 3px;
}

.menu-title {
    color: #7dd3c7;
    font-size: 10px;
    font-weight: bold;
    letter-spacing: 1px;
    padding: 0 12px;
    margin-bottom: 10px;
}

.section-space {
    margin-top: 30px;
}

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
    box-sizing: border-box;

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
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(4px);
}

.menu.active {
    background: white;
    color: #047857;
    font-weight: bold;
    transform: none;
}

.menu.active .menu-icon {
    color: #047857;
}

.menu-icon {
    width: 25px;
    min-width: 25px;
    text-align: center;
    font-size: 18px;
}

.menu.logout {
    margin-top: 25px;
    color: #fecaca;
}

.menu.logout:hover {
    background: rgba(239, 68, 68, 0.15);
    color: #fecaca;
    transform: translateX(4px);
}
/* =========================
   ICON
========================= */

.menu-icon {
    width: 25px;

    min-width: 25px;

    text-align: center;

    font-size: 18px;
}


/* =========================
   LOGOUT
========================= */

.menu.logout {
    margin-top: 25px;

    color: #fecaca;
}

.menu.logout:hover {
    background: rgba(239,68,68,0.15);

    color: #fecaca;

    transform: translateX(4px);
}


/* =========================
   MAIN
========================= */

.main {
    margin-left: 250px;

    padding: 30px;
}

/* =========================
   MENU
========================= */

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

    transition: 0.3s;
}


/* ICON */

.menu-icon {
    width: 25px;
    min-width: 25px;

    text-align: center;

    font-size: 18px;
}


/* HOVER */

.menu:hover {
    background: rgba(255,255,255,0.15);

    transform: translateX(4px);
}


/* MENU AKTIF */

.menu.active {
    background: white;

    color: #047857;

    font-weight: bold;

    transform: none;
}

.menu.active .menu-icon {
    color: #047857;
}


/* LOGOUT */

.menu.logout {
    margin-top: 25px;

    color: #fecaca;
}

.menu.logout:hover {
    background: rgba(239,68,68,0.15);

    color: #fecaca;
}


/* =========================
   MAIN
========================= */

.main {
    margin-left: 250px;

    min-height: 100vh;

    padding: 30px;
}


/* =========================
   HEADER
========================= */

.header {
    background: white;

    border-radius: 18px;

    padding: 25px;

    margin-bottom: 25px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,0.06);
}

.header-small {
    color: #10b981;

    font-size: 11px;

    font-weight: bold;

    margin-bottom: 7px;
}

.header h1 {
    color: #123b36;

    font-size: 27px;
}

.header p {
    color: #64748b;

    font-size: 13px;

    margin-top: 7px;
}


/* USER */

.user-box {
    display: flex;

    align-items: center;

    gap: 10px;
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

    font-size: 18px;
}


/* =========================
   CARD
========================= */

.card {
    background: white;

    border-radius: 18px;

    overflow: hidden;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,0.06);
}

.card-header {
    padding: 22px 25px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    border-bottom:
        1px solid #edf2f0;
}

.card-header h2 {
    color: #123b36;

    font-size: 18px;
}

.card-header p {
    color: #94a3b8;

    font-size: 11px;

    margin-top: 5px;
}


/* =========================
   BUTTON
========================= */

.btn {
    display: inline-flex;

    align-items: center;

    height: 40px;

    padding: 0 15px;

    border-radius: 8px;

    text-decoration: none;

    font-size: 12px;

    font-weight: bold;
}

.btn-green {
    background: #059669;

    color: white;
}

.btn-green:hover {
    background: #047857;
}


/* =========================
   SEARCH
========================= */

.search {
    padding: 20px 25px;

    background: #f8faf9;
}

.search form {
    display: flex;

    gap: 10px;
}

.search input {
    flex: 1;

    height: 44px;

    border:
        1px solid #dbe3e0;

    border-radius: 9px;

    padding: 0 15px;

    outline: none;

    font-size: 13px;
}

.search input:focus {
    border-color: #10b981;
}

.search button {
    border: none;

    background: #059669;

    color: white;

    padding: 0 20px;

    border-radius: 9px;

    font-weight: bold;

    cursor: pointer;
}

.search button:hover {
    background: #047857;
}

.reset {
    display: flex;

    align-items: center;

    padding: 0 18px;

    background: #e2e8f0;

    color: #475569;

    text-decoration: none;

    border-radius: 9px;

    font-size: 12px;
}


/* =========================
   TABLE
========================= */

.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;

    border-collapse: collapse;

    min-width: 800px;
}


/* HEADER TABEL
   SAMA DENGAN DASHBOARD */

thead {
    background: #ecfdf5;
}

th {
    color: #047857;

    padding: 14px;

    font-size: 11px;

    text-align: left;

    border-bottom:
        2px solid #d1fae5;
}


/* ISI TABEL */

td {
    padding: 14px;

    font-size: 12px;

    border-bottom:
        1px solid #edf2f0;

    color: #475569;
}


/* HOVER BARIS */

tbody tr:hover {
    background: #f0fdf9;
}


/* JAM SHOLAT */

.time {
    display: inline-block;

    background: #f0fdf4;

    color: #047857;

    padding: 6px 9px;

    border-radius: 6px;

    font-size: 11px;

    font-weight: bold;
}


/* DATA KOSONG */

.empty {
    text-align: center;

    padding: 60px !important;

    color: #94a3b8;
}


/* =========================
   FOOTER
========================= */

.footer {
    text-align: center;

    margin-top: 25px;

    color: #94a3b8;

    font-size: 11px;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:750px) {

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

    .user-box {
        display: none;
    }

    .header {
        padding: 20px;
    }

    .search form {
        flex-direction: column;
    }

    .search button,
    .reset {
        height: 42px;

        justify-content: center;
    }
}

</style>

</head>

<body>


<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <!-- LOGO -->
    <div class="logo">
        <div class="logo-icon">🕌</div>

        <div class="logo-text">
            <h2>SholatKu</h2>
            <span>Pengingat Sholat</span>
        </div>
    </div>


    <!-- MENU UTAMA -->
    <div class="menu-title">MENU UTAMA</div>

    <ul class="menu-list">

        <li>
            <a href="dashboard.php"
               class="menu <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <span class="menu-icon">🏠</span>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="jadwal.php"
               class="menu <?= basename($_SERVER['PHP_SELF']) == 'jadwal.php' ? 'active' : ''; ?>">
                <span class="menu-icon">🕌</span>
                <span>Jadwal Sholat</span>
            </a>
        </li>

        <li>
            <a href="tambah.php"
               class="menu <?= basename($_SERVER['PHP_SELF']) == 'tambah.php' ? 'active' : ''; ?>">
                <span class="menu-icon">➕</span>
                <span>Tambah Jadwal</span>
            </a>
        </li>

        <li>
            <a href="kiblat.php"
               class="menu <?= basename($_SERVER['PHP_SELF']) == 'kiblat.php' ? 'active' : ''; ?>">
                <span class="menu-icon">🧭</span>
                <span>Arah Kiblat</span>
            </a>
        </li>

    </ul>


    <!-- AKUN -->
    <div class="menu-title section-space">AKUN</div>

    <ul class="menu-list">

        <li>
            <a href="profil.php"
               class="menu <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : ''; ?>">
                <span class="menu-icon">👤</span>
                <span>Profil</span>
            </a>
        </li>

        <li>
            <a href="pengaturan.php"
               class="menu <?= basename($_SERVER['PHP_SELF']) == 'pengaturan.php' ? 'active' : ''; ?>">
                <span class="menu-icon">⚙️</span>
                <span>Pengaturan Akun</span>
            </a>
        </li>

    </ul>


    <!-- LAPORAN -->
    <div class="menu-title section-space">LAPORAN</div>

    <ul class="menu-list">

        <li>
            <a href="print.php"
               class="menu <?= basename($_SERVER['PHP_SELF']) == 'print.php' ? 'active' : ''; ?>">
                <span class="menu-icon">🖨️</span>
                <span>Print Jadwal</span>
            </a>
        </li>

    </ul>


    <!-- LOGOUT -->
    <ul class="menu-list">

        <li>
            <a href="../logout.php" class="menu logout">
                <span class="menu-icon">🚪</span>
                <span>Logout</span>
            </a>
        </li>

    </ul>

</aside>


<!-- =========================
     MAIN
========================= -->

<main class="main">


    <!-- HEADER -->

    <div class="header">

        <div>

            <div class="header-small">
                JADWAL SAYA
            </div>

            <h1>
                Jadwal Sholat
            </h1>

            <p>
                Kelola jadwal sholat Anda.
            </p>

        </div>


        <div class="user-box">

            <div class="avatar">

                <?= strtoupper(
                    substr($nama, 0, 1)
                ); ?>

            </div>

            <strong>
                <?= htmlspecialchars($nama); ?>
            </strong>

        </div>

    </div>



    <!-- CARD -->

    <div class="card">


        <div class="card-header">

            <div>

                <h2>
                    🕌 Jadwal Sholat Saya
                </h2>

                <p>
                    Semua jadwal yang diberikan untuk akun Anda.
                </p>

            </div>


            <a
                href="tambah.php"
                class="btn btn-green"
            >
                ➕ Tambah Jadwal
            </a>

        </div>



        <!-- SEARCH -->

        <div class="search">

            <form method="GET">

                <input
                    type="text"
                    name="cari"
                    value="<?= htmlspecialchars($cari); ?>"
                    placeholder="🔎 Cari berdasarkan tanggal..."
                >

                <button type="submit">
                    🔎 Cari
                </button>


                <a
                    href="jadwal.php"
                    class="reset"
                >
                    Reset
                </a>

            </form>

        </div>



        <!-- TABLE -->

        <div class="table-wrap">

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

                if (
                    $data &&
                    mysqli_num_rows($data) > 0
                ):

                    while (
                        $row =
                        mysqli_fetch_assoc($data)
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


                        <td>
                            <span class="time">
                                <?= htmlspecialchars(
                                    $row['subuh']
                                ); ?>
                            </span>
                        </td>


                        <td>
                            <span class="time">
                                <?= htmlspecialchars(
                                    $row['dzuhur']
                                ); ?>
                            </span>
                        </td>


                        <td>
                            <span class="time">
                                <?= htmlspecialchars(
                                    $row['ashar']
                                ); ?>
                            </span>
                        </td>


                        <td>
                            <span class="time">
                                <?= htmlspecialchars(
                                    $row['maghrib']
                                ); ?>
                            </span>
                        </td>


                        <td>
                            <span class="time">
                                <?= htmlspecialchars(
                                    $row['isya']
                                ); ?>
                            </span>
                        </td>

                    </tr>

                <?php

                    endwhile;

                else:

                ?>

                    <tr>

                        <td
                            colspan="7"
                            class="empty"
                        >

                            📭

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

        © <?= date('Y'); ?>

        SholatKu · Aplikasi Pengingat Sholat

    </div>


</main>

</body>

</html>