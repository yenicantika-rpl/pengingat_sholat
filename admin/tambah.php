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

$pesan = "";
$tipe_pesan = "";


/* =========================
   AMBIL DATA USER
========================= */

$query_user = mysqli_query(
    $koneksi,
    "SELECT id, nama, email
     FROM users
     WHERE role = 'user'
     ORDER BY nama ASC"
);


/* =========================
   SIMPAN JADWAL
========================= */

if (isset($_POST['simpan'])) {

    $user_id = (int) ($_POST['user_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';

    $subuh   = $_POST['subuh'] ?? '';
    $dzuhur  = $_POST['dzuhur'] ?? '';
    $ashar   = $_POST['ashar'] ?? '';
    $maghrib = $_POST['maghrib'] ?? '';
    $isya    = $_POST['isya'] ?? '';


    if (
        $user_id == 0 ||
        empty($tanggal) ||
        empty($subuh) ||
        empty($dzuhur) ||
        empty($ashar) ||
        empty($maghrib) ||
        empty($isya)
    ) {

        $pesan = "Semua data wajib diisi.";
        $tipe_pesan = "error";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO jadwal_sholat
            (user_id, tanggal, subuh, dzuhur, ashar, maghrib, isya)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issssss",
            $user_id,
            $tanggal,
            $subuh,
            $dzuhur,
            $ashar,
            $maghrib,
            $isya
        );

        if (mysqli_stmt_execute($stmt)) {

            $pesan = "Jadwal sholat berhasil ditambahkan.";
            $tipe_pesan = "success";

            $_POST = [];

        } else {

            $pesan = "Gagal menyimpan jadwal.";
            $tipe_pesan = "error";
        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Tambah Jadwal | SholatKu</title>


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
   FORM PANEL
===================================== */

.panel {
    background: white;

    border-radius: 17px;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,.05);

    padding: 25px;
}


.panel-header {
    padding-bottom: 20px;

    border-bottom:
        1px solid #eef2f1;

    margin-bottom: 25px;
}


.panel-header h2 {
    font-size: 18px;

    color: #123b36;

    margin-bottom: 5px;
}


.panel-header p {
    color: #94a3b8;

    font-size: 11px;
}


/* =====================================
   ALERT
===================================== */

.alert {
    padding: 14px 16px;

    border-radius: 10px;

    margin-bottom: 20px;

    font-size: 13px;
}


.alert.success {
    background: #dcfce7;

    color: #166534;

    border: 1px solid #bbf7d0;
}


.alert.error {
    background: #fee2e2;

    color: #991b1b;

    border: 1px solid #fecaca;
}


/* =====================================
   FORM
===================================== */

.form-group {
    margin-bottom: 20px;
}


.form-group label {
    display: block;

    color: #334155;

    font-size: 13px;

    font-weight: bold;

    margin-bottom: 8px;
}


.form-group input,
.form-group select {
    width: 100%;

    padding: 12px 14px;

    border: 1px solid #dbe3e0;

    border-radius: 10px;

    background: white;

    color: #1f2937;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 13px;

    outline: none;

    transition: .25s;
}


.form-group input:focus,
.form-group select:focus {
    border-color: #10b981;

    box-shadow:
        0 0 0 3px
        rgba(16,185,129,.10);
}


/* =====================================
   WAKTU SHOLAT
===================================== */

.waktu-title {
    color: #123b36;

    font-size: 16px;

    font-weight: bold;

    margin-top: 25px;

    margin-bottom: 15px;
}


.waktu-grid {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 18px;
}


.waktu-item {
    background: #f8faf9;

    border: 1px solid #e5ebe9;

    border-radius: 12px;

    padding: 18px;
}


.waktu-item label {
    display: block;

    color: #475569;

    font-size: 12px;

    font-weight: bold;

    margin-bottom: 8px;
}


.waktu-item input {
    width: 100%;

    padding: 11px 13px;

    border: 1px solid #dbe3e0;

    border-radius: 9px;

    background: white;

    font-size: 13px;

    outline: none;
}


.waktu-item input:focus {
    border-color: #10b981;

    box-shadow:
        0 0 0 3px
        rgba(16,185,129,.10);
}


/* =====================================
   BUTTON
===================================== */

.form-actions {
    display: flex;

    gap: 10px;

    margin-top: 25px;

    padding-top: 20px;

    border-top:
        1px solid #eef2f1;
}


.btn-simpan {
    border: none;

    background: #047857;

    color: white;

    padding: 12px 22px;

    border-radius: 10px;

    font-size: 13px;

    font-weight: bold;

    cursor: pointer;

    transition: .25s;
}


.btn-simpan:hover {
    background: #065f46;

    transform: translateY(-2px);
}


.btn-kembali {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    background: #f1f5f9;

    color: #475569;

    text-decoration: none;

    padding: 12px 22px;

    border-radius: 10px;

    font-size: 13px;

    font-weight: bold;

    transition: .25s;
}


.btn-kembali:hover {
    background: #e2e8f0;
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

    .waktu-grid {
        grid-template-columns: 1fr;
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
               class="menu active">

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
                Tambah Jadwal
            </h1>

            <p>
                Tambahkan jadwal sholat untuk pengguna.
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
         FORM
    ====================================== -->

    <div class="panel">


        <div class="panel-header">

            <h2>
                Form Tambah Jadwal
            </h2>

            <p>
                Isi data jadwal sholat dengan lengkap.
            </p>

        </div>


        <?php if ($pesan != ""): ?>

            <div class="alert <?= $tipe_pesan; ?>">

                <?= htmlspecialchars($pesan); ?>

            </div>

        <?php endif; ?>



        <form method="POST">


            <!-- USER -->

            <div class="form-group">

                <label for="user_id">
                    Pilih Pengguna
                </label>

                <select
                    name="user_id"
                    id="user_id"
                    required
                >

                    <option value="">
                        -- Pilih Pengguna --
                    </option>


                    <?php while (
                        $user = mysqli_fetch_assoc($query_user)
                    ): ?>

                        <option
                            value="<?= $user['id']; ?>"
                        >

                            <?= htmlspecialchars(
                                $user['nama']
                            ); ?>

                            -
                            
                            <?= htmlspecialchars(
                                $user['email']
                            ); ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>



            <!-- TANGGAL -->

            <div class="form-group">

                <label for="tanggal">
                    Tanggal
                </label>

                <input
                    type="date"
                    name="tanggal"
                    id="tanggal"
                    required
                >

            </div>



            <!-- WAKTU -->

            <div class="waktu-title">
                Waktu Sholat
            </div>


            <div class="waktu-grid">


                <!-- SUBUH -->

                <div class="waktu-item">

                    <label for="subuh">
                        Subuh
                    </label>

                    <input
                        type="time"
                        name="subuh"
                        id="subuh"
                        required
                    >

                </div>



                <!-- DZUHUR -->

                <div class="waktu-item">

                    <label for="dzuhur">
                        Dzuhur
                    </label>

                    <input
                        type="time"
                        name="dzuhur"
                        id="dzuhur"
                        required
                    >

                </div>



                <!-- ASHAR -->

                <div class="waktu-item">

                    <label for="ashar">
                        Ashar
                    </label>

                    <input
                        type="time"
                        name="ashar"
                        id="ashar"
                        required
                    >

                </div>



                <!-- MAGHRIB -->

                <div class="waktu-item">

                    <label for="maghrib">
                        Maghrib
                    </label>

                    <input
                        type="time"
                        name="maghrib"
                        id="maghrib"
                        required
                    >

                </div>



                <!-- ISYA -->

                <div class="waktu-item">

                    <label for="isya">
                        Isya
                    </label>

                    <input
                        type="time"
                        name="isya"
                        id="isya"
                        required
                    >

                </div>

            </div>



            <!-- BUTTON -->

            <div class="form-actions">

                <button
                    type="submit"
                    name="simpan"
                    class="btn-simpan"
                >
                    Simpan Jadwal
                </button>


                <a
                    href="jadwal.php"
                    class="btn-kembali"
                >
                    Kembali
                </a>

            </div>


        </form>

    </div>



    <!-- FOOTER -->

    <div class="footer">

        © <?= date('Y'); ?> SholatKu
        · Aplikasi Pengingat Sholat

    </div>


</main>


</body>

</html>