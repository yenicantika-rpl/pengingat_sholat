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

/* =========================
   AMBIL DATA USER
========================= */
$query = mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE id = $user_id LIMIT 1"
);

$user = mysqli_fetch_assoc($query);

if (!$user) {
    echo "Data pengguna tidak ditemukan.";
    exit;
}

$nama = $user['nama'] ?? 'User';
$email = $user['email'] ?? '-';
$role = $user['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Profil | SholatKu</title>

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

/* =========================
   SIDEBAR
========================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;

    width: 250px;
    height: 100vh;

    background: linear-gradient(
        180deg,
        #064e3b,
        #047857
    );

    padding: 25px 15px;

    color: white;

    box-shadow: 4px 0 15px rgba(0,0,0,0.08);

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

    background: #047857;
    color: #047857;

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
    font-size: 11px;

    color: #a7f3d0;

    font-weight: bold;

    padding: 10px 15px;

    margin-top: 5px;

    text-transform: uppercase;
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

    color: white;

    text-decoration: none;

    padding: 13px 15px;

    border-radius: 12px;

    font-size: 14px;

    font-weight: 500;

    transition: 0.3s;
}

.menu:hover {
    background: rgba(255,255,255,0.15);
    transform: translateX(4px);
}

/* MENU AKTIF */

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

.logout {
    margin-top: 10px;
    color: #fee2e2;
}

.logout:hover {
    background: rgba(239,68,68,0.18);
    color: white;
}

/* =========================
   MAIN
========================= */

.main {
    margin-left: 250px;

    padding: 30px;

    min-height: 100vh;
}

/* =========================
   HEADER
========================= */

.header {
    background: white;

    border-radius: 18px;

    padding: 25px 30px;

    margin-bottom: 25px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.05);

    display: flex;

    justify-content: space-between;

    align-items: center;
}

.header h1 {
    color: #047857;

    font-size: 27px;

    margin-bottom: 7px;
}

.header p {
    color: #64748b;

    font-size: 14px;
}

.user-name {
    background: #ecfdf5;

    color: #047857;

    padding: 10px 16px;

    border-radius: 10px;

    font-size: 14px;

    font-weight: bold;
}

/* =========================
   PROFILE CARD
========================= */

.profile-card {
    background: white;

    border-radius: 18px;

    padding: 30px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.05);

    max-width: 850px;
}

/* =========================
   PROFILE HEADER
========================= */

.profile-top {
    display: flex;

    align-items: center;

    gap: 20px;

    padding-bottom: 25px;

    margin-bottom: 25px;

    border-bottom: 1px solid #e2e8f0;
}

.avatar {
    width: 75px;
    height: 75px;

    border-radius: 20px;

    background: #ecfdf5;

    color: #047857;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 35px;
}

.profile-top h2 {
    color: #047857;

    font-size: 22px;

    margin-bottom: 5px;
}

.profile-top p {
    color: #64748b;

    font-size: 14px;
}

/* =========================
   DATA PROFILE
========================= */

.profile-table {
    width: 100%;

    border-collapse: separate;

    border-spacing: 0;

    overflow: hidden;

    border: 1px solid #e2e8f0;

    border-radius: 12px;
}

.profile-table tr:not(:last-child) td {
    border-bottom: 1px solid #e2e8f0;
}

.profile-table td {
    padding: 17px;

    font-size: 14px;
}

.profile-table td:first-child {
    width: 35%;

    background: #ecfdf5;

    color: #047857;

    font-weight: bold;
}

.profile-table td:last-child {
    background: white;

    color: #475569;
}

.role {
    display: inline-block;

    background: #dcfce7;

    color: #166534;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;

    text-transform: capitalize;
}

/* =========================
   INFO
========================= */

.info-box {
    margin-top: 25px;

    padding: 18px;

    background: #ecfdf5;

    border-left: 4px solid #10b981;

    border-radius: 10px;

    color: #065f46;

    font-size: 13px;

    line-height: 1.7;
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 800px) {

    .sidebar {
        width: 210px;
    }

    .main {
        margin-left: 210px;
        padding: 20px;
    }

    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}

@media (max-width: 600px) {

    .sidebar {
        position: relative;

        width: 100%;

        height: auto;
    }

    .main {
        margin-left: 0;

        padding: 15px;
    }

    .profile-card {
        padding: 20px;
    }

    .profile-top {
        flex-direction: column;

        align-items: flex-start;
    }

    .profile-table td {
        padding: 13px;
    }

    .profile-table td:first-child {
        width: 40%;
    }
}

</style>

</head>

<body>

<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

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
            <a href="dashboard.php" class="menu">
                <span class="menu-icon">🏠</span>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="jadwal.php" class="menu">
                <span class="menu-icon">🕌</span>
                <span>Jadwal Sholat</span>
            </a>
        </li>

        <li>
            <a href="tambah.php" class="menu">
                <span class="menu-icon">➕</span>
                <span>Tambah Jadwal</span>
            </a>
        </li>

        <li>
            <a href="kiblat.php" class="menu">
                <span class="menu-icon">🧭</span>
                <span>Arah Kiblat</span>
            </a>
        </li>
    </ul>

    <!-- AKUN -->
    <div class="menu-title section-space">AKUN</div>

    <ul class="menu-list">
        <li>
            <a href="profil.php" class="menu active">
                <span class="menu-icon">👤</span>
                <span>Profil</span>
            </a>
        </li>

        <li>
            <a href="pengaturan.php" class="menu">
                <span class="menu-icon">⚙️</span>
                <span>Pengaturan Akun</span>
            </a>
        </li>
    </ul>

    <!-- LAPORAN -->
    <div class="menu-title section-space">LAPORAN</div>

    <ul class="menu-list">
        <li>
            <a href="print.php" class="menu">
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

            <h1>Profil Saya</h1>

            <p>
                Informasi akun pengguna SholatKu.
            </p>

        </div>


        <div class="user-name">

            👤 <?php echo htmlspecialchars($nama); ?>

        </div>

    </div>


    <!-- PROFILE CARD -->

    <div class="profile-card">


        <div class="profile-top">

            <div class="avatar">
                👤
            </div>

            <div>

                <h2>
                    <?php echo htmlspecialchars($nama); ?>
                </h2>

                <p>
                    Pengguna aplikasi SholatKu
                </p>

            </div>

        </div>


        <!-- DATA PROFILE -->

        <table class="profile-table">

            <tr>

                <td>
                    👤 Nama Lengkap
                </td>

                <td>
                    <?php echo htmlspecialchars($nama); ?>
                </td>

            </tr>


            <tr>

                <td>
                    📧 Email
                </td>

                <td>
                    <?php echo htmlspecialchars($email); ?>
                </td>

            </tr>


            <tr>

                <td>
                    🔐 Level Akun
                </td>

                <td>

                    <span class="role">

                        <?php echo htmlspecialchars($role); ?>

                    </span>

                </td>

            </tr>

        </table>


        <div class="info-box">

            <strong>💡 Informasi Akun</strong>

            Data di atas merupakan informasi akun yang sedang
            digunakan untuk masuk ke aplikasi SholatKu.

        </div>


    </div>

</main>

</body>

</html>