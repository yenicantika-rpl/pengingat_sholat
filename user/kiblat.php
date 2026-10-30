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

$nama_user = $_SESSION['nama'] ?? 'User';


/* =========================
   DATA LOKASI
   TANPA JAVASCRIPT
========================= */

$lokasi = [

    "Sumedang" => [
    "latitude" => -6.85861,
    "longitude" => 107.91639
],
    "Bandung" => [
        "latitude" => -6.9175,
        "longitude" => 107.6191
    ],

    "Jakarta" => [
        "latitude" => -6.2088,
        "longitude" => 106.8456
    ],

    "Bogor" => [
        "latitude" => -6.5971,
        "longitude" => 106.8060
    ],

    "Depok" => [
        "latitude" => -6.4025,
        "longitude" => 106.7942
    ],

    "Bekasi" => [
        "latitude" => -6.2383,
        "longitude" => 106.9756
    ],

    "Garut" => [
        "latitude" => -7.2167,
        "longitude" => 107.9000
    ],

    "Ciamis" => [
        "latitude" => -7.3333,
        "longitude" => 108.3500
    ],

    "Cirebon" => [
        "latitude" => -6.7320,
        "longitude" => 108.5523
    ],

    "Yogyakarta" => [
        "latitude" => -7.7956,
        "longitude" => 110.3695
    ],

    "Surabaya" => [
        "latitude" => -7.2575,
        "longitude" => 112.7521
    ],

    "Semarang" => [
        "latitude" => -6.9667,
        "longitude" => 110.4167
    ],

    "Medan" => [
        "latitude" => 3.5952,
        "longitude" => 98.6722
    ],

    "Makassar" => [
        "latitude" => -5.1477,
        "longitude" => 119.4327
    ]

];


/* =========================
   KOORDINAT KA'BAH
========================= */

$kaabah_lat = 21.4225;
$kaabah_lon = 39.8262;


/* =========================
   VARIABEL
========================= */

$nama_lokasi = "";
$latitude = "";
$longitude = "";
$arah_kiblat = null;


/* =========================
   PROSES PILIH LOKASI
========================= */

if (isset($_POST['hitung'])) {

    $nama_lokasi = $_POST['lokasi'] ?? "";

    if (isset($lokasi[$nama_lokasi])) {

        $latitude = $lokasi[$nama_lokasi]['latitude'];
        $longitude = $lokasi[$nama_lokasi]['longitude'];


        /* =========================
           HITUNG ARAH KIBLAT
        ========================= */

        $lat1 = deg2rad($latitude);
        $lon1 = deg2rad($longitude);

        $lat2 = deg2rad($kaabah_lat);
        $lon2 = deg2rad($kaabah_lon);

        $delta_lon = $lon2 - $lon1;

        $y = sin($delta_lon) * cos($lat2);

        $x =
            cos($lat1) * sin($lat2)
            -
            sin($lat1) * cos($lat2) * cos($delta_lon);

        $bearing = atan2($y, $x);

        $bearing = rad2deg($bearing);

        $arah_kiblat = ($bearing + 360) % 360;

        $arah_kiblat = round($arah_kiblat, 1);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Arah Kiblat | SholatKu</title>


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

    font-family: Arial, Helvetica, sans-serif;

    background: #f4f7f6;

    color: #1f2937;
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

    overflow-y: auto;

    z-index: 1000;
}


/* =========================
   LOGO
========================= */

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

    font-size: 25px;
}


.logo-text h2 {

    font-size: 20px;

    color: white;
}


.logo-text p {

    color: #a7f3d0;

    font-size: 11px;

    margin-top: 3px;
}


/* =========================
   MENU TITLE
========================= */

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


/* =========================
   MENU
========================= */

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

    margin-top: 25px;

    color: #fecaca;
}


.menu.logout:hover {

    background: rgba(239,68,68,.15);

    color: white;
}


/* =========================
   MAIN
========================= */

.main {

    margin-left: 260px;

    min-height: 100vh;

    padding: 30px;
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

    font-size: 28px;

    color: #102a27;

    margin-bottom: 7px;
}


.header p {

    color: #64748b;

    font-size: 14px;
}


/* =========================
   USER BOX
========================= */

.user-box {

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

    font-size: 18px;
}


.user-box strong {

    display: block;

    font-size: 13px;
}


.user-box span {

    color: #94a3b8;

    font-size: 11px;
}


/* =========================
   KIBLAT CARD
========================= */

.kiblat-container {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 25px;
}


/* =========================
   CARD
========================= */

.card {

    background: white;

    border-radius: 18px;

    padding: 28px;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,.06);
}


/* =========================
   CARD TITLE
========================= */

.card-title {

    display: flex;

    align-items: center;

    gap: 12px;

    margin-bottom: 25px;
}


.card-icon {

    width: 45px;
    height: 45px;

    background: #d1fae5;

    border-radius: 12px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 22px;
}


.card-title h2 {

    font-size: 18px;

    color: #123b36;

    margin-bottom: 4px;
}


.card-title p {

    color: #94a3b8;

    font-size: 11px;
}


/* =========================
   FORM
========================= */

.form-group {

    margin-bottom: 20px;
}


.form-group label {

    display: block;

    font-size: 12px;

    font-weight: bold;

    color: #334155;

    margin-bottom: 8px;
}


.form-group select {

    width: 100%;

    padding: 13px 14px;

    border: 1px solid #dbe3e0;

    border-radius: 10px;

    background: white;

    color: #334155;

    font-size: 13px;

    outline: none;
}


.form-group select:focus {

    border-color: #10b981;

    box-shadow:
        0 0 0 3px
        rgba(16,185,129,.12);
}


/* =========================
   BUTTON
========================= */

.btn-hitung {

    width: 100%;

    border: none;

    background: #047857;

    color: white;

    padding: 13px;

    border-radius: 10px;

    font-size: 13px;

    font-weight: bold;

    cursor: pointer;

    transition: .25s;
}


.btn-hitung:hover {

    background: #065f46;

    transform: translateY(-2px);
}


/* =========================
   HASIL
========================= */

.hasil {

    text-align: center;
}


.kaabah {

    font-size: 55px;

    margin-bottom: 15px;
}


.hasil h2 {

    color: #123b36;

    font-size: 20px;

    margin-bottom: 8px;
}


.derajat {

    font-size: 48px;

    font-weight: bold;

    color: #047857;

    margin: 15px 0;
}


.derajat span {

    font-size: 18px;

    color: #64748b;
}


.lokasi {

    background: #ecfdf5;

    border: 1px solid #d1fae5;

    border-radius: 12px;

    padding: 15px;

    margin-top: 20px;

    color: #047857;

    font-size: 12px;
}


.koordinat {

    margin-top: 10px;

    color: #64748b;

    font-size: 11px;
}


/* =========================
   INFO
========================= */

.info {

    margin-top: 25px;

    background: #f0fdf4;

    border-left: 4px solid #10b981;

    padding: 15px;

    border-radius: 8px;

    color: #475569;

    font-size: 11px;

    line-height: 1.7;
}


/* =========================
   FOOTER
========================= */

.footer {

    margin-top: 25px;

    text-align: center;

    color: #94a3b8;

    font-size: 11px;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width: 800px) {

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

    .kiblat-container {

        grid-template-columns: 1fr;
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

        <div class="logo-icon">
            🕌
        </div>

        <div class="logo-text">

            <h2>
                SholatKu
            </h2>

            <p>
                Pengingat Sholat
            </p>

        </div>

    </div>


    <div class="menu-title">
        USER PANEL
    </div>


    <a href="dashboard.php"
       class="menu">

        <span class="menu-icon">
            🏠
        </span>

        <span>
            Dashboard
        </span>

    </a>


    <a href="tambah.php"
       class="menu">

        <span class="menu-icon">
            ➕
        </span>

        <span>
            Tambah Jadwal
        </span>

    </a>


    <a href="jadwal.php"
       class="menu">

        <span class="menu-icon">
            🕌
        </span>

        <span>
            Jadwal Sholat
        </span>

    </a>


    <a href="kiblat.php"
       class="menu active">

        <span class="menu-icon">
            🕋
        </span>

        <span>
            Arah Kiblat
        </span>

    </a>


    <div class="menu-title section-space">
        AKUN
    </div>


    <a href="profil.php"
       class="menu">

        <span class="menu-icon">
            👤
        </span>

        <span>
            Profil
        </span>

    </a>


    <a href="print.php"
       class="menu">

        <span class="menu-icon">
            🖨️
        </span>

        <span>
            Print Jadwal
        </span>

    </a>


    <a href="../logout.php"
       class="menu logout">

        <span class="menu-icon">
            🚪
        </span>

        <span>
            Logout
        </span>

    </a>


</aside>



<!-- =========================
     MAIN
========================= -->

<main class="main">


    <!-- HEADER -->

    <div class="header">

        <div>

            <div class="header-small">
                FITUR IBADAH
            </div>

            <h1>
                Arah Kiblat
            </h1>

            <p>
                Tentukan arah kiblat berdasarkan lokasi Anda.
            </p>

        </div>


        <div class="user-box">

            <div class="avatar">

                <?= strtoupper(
                    substr($nama_user, 0, 1)
                ); ?>

            </div>

            <div>

                <strong>
                    <?= htmlspecialchars($nama_user); ?>
                </strong>

                <span>
                    Pengguna
                </span>

            </div>

        </div>

    </div>



    <!-- =========================
         KIBLAT
    ========================= -->

    <div class="kiblat-container">


        <!-- PILIH LOKASI -->

        <div class="card">

            <div class="card-title">

                <div class="card-icon">
                    📍
                </div>

                <div>

                    <h2>
                        Lokasi Anda
                    </h2>

                    <p>
                        Pilih kota tempat Anda berada.
                    </p>

                </div>

            </div>


            <form method="POST">


                <div class="form-group">

                    <label>
                        PILIH KOTA
                    </label>

                    <select
                        name="lokasi"
                        required
                    >

                        <option value="">
                            -- Pilih Kota --
                        </option>


                        <?php foreach ($lokasi as $kota => $koordinat): ?>

                            <option
                                value="<?= htmlspecialchars($kota); ?>"
                                <?= $nama_lokasi === $kota ? 'selected' : ''; ?>
                            >

                                <?= htmlspecialchars($kota); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <button
                    type="submit"
                    name="hitung"
                    class="btn-hitung"
                >

                    🧭 Hitung Arah Kiblat

                </button>


            </form>


            
        </div>



        <!-- HASIL -->

        <div class="card hasil">

            <div class="kaabah">
                🕋
            </div>


            <?php if ($arah_kiblat !== null): ?>

                <h2>
                    Arah Kiblat
                </h2>


                <div class="derajat">

                    <?= $arah_kiblat; ?>

                    <span>
                        °
                    </span>

                </div>


                <p>
                    dari arah Utara
                </p>


                <div class="lokasi">

                    📍 Lokasi:
                    <b>
                        <?= htmlspecialchars($nama_lokasi); ?>
                    </b>


                    <div class="koordinat">

                        Latitude:
                        <?= $latitude; ?>

                        <br>

                        Longitude:
                        <?= $longitude; ?>

                    </div>

                </div>

            <?php else: ?>

                <h2>
                    Arah Kiblat
                </h2>

                <p>
                    Silakan pilih lokasi terlebih dahulu.
                </p>

                <div class="derajat">
                    --°
                </div>

                <div class="lokasi">

                    🕋

                    <br>

                    Arah kiblat akan tampil
                    setelah lokasi dipilih.

                </div>

            <?php endif; ?>

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