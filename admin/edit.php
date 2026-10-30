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

/* =========================
   CEK ID JADWAL
========================= */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: jadwal.php");
    exit;
}

$id = (int) $_GET['id'];


/* =========================
   AMBIL DATA JADWAL
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM jadwal_sholat
     WHERE id = $id
     LIMIT 1"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data jadwal tidak ditemukan.";
    exit;
}


/* =========================
   AMBIL DATA USER
========================= */

$users = mysqli_query(
    $koneksi,
    "SELECT id, nama, email
     FROM users
     WHERE role = 'user'
     ORDER BY nama ASC"
);


/* =========================
   PROSES UPDATE
========================= */

$error = "";

if (isset($_POST['update'])) {

    $user_id = (int) $_POST['user_id'];
    $tanggal = $_POST['tanggal'];
    $subuh = $_POST['subuh'];
    $dzuhur = $_POST['dzuhur'];
    $ashar = $_POST['ashar'];
    $maghrib = $_POST['maghrib'];
    $isya = $_POST['isya'];

    if (
        $user_id == 0 ||
        empty($tanggal) ||
        empty($subuh) ||
        empty($dzuhur) ||
        empty($ashar) ||
        empty($maghrib) ||
        empty($isya)
    ) {

        $error = "Semua data harus diisi.";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE jadwal_sholat
             SET user_id = ?,
                 tanggal = ?,
                 subuh = ?,
                 dzuhur = ?,
                 ashar = ?,
                 maghrib = ?,
                 isya = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issssssi",
            $user_id,
            $tanggal,
            $subuh,
            $dzuhur,
            $ashar,
            $maghrib,
            $isya,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: jadwal.php?success=updated");
            exit;

        } else {

            $error = "Gagal memperbarui jadwal.";
        }
    }
}

$nama_admin = $_SESSION['nama'] ?? 'Administrator';

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Edit Jadwal | SholatKu</title>


<style>

/* =========================
   RESET
========================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {

    font-family:
        Arial,
        Helvetica,
        sans-serif;

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

    background:
        linear-gradient(
            180deg,
            #063b35,
            #052e2a
        );

    color: white;

    padding: 25px 18px;

    box-shadow:
        5px 0 20px
        rgba(0,0,0,.08);
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

    border-radius: 14px;

    background: #10b981;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 25px;
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

    text-decoration: none;

    color: #d1fae5;

    padding: 13px 14px;

    margin-bottom: 6px;

    border-radius: 10px;

    font-size: 14px;

    transition: .2s;
}


.menu:hover {

    background:
        rgba(255,255,255,.08);

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

    min-height: 100vh;

    padding: 30px;
}


/* =========================
   TOP HEADER
========================= */

.header {

    background: white;

    border-radius: 18px;

    padding: 22px 27px;

    margin-bottom: 25px;

    display: flex;

    align-items: center;

    justify-content: space-between;

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

    font-size: 26px;
}


.header p {

    color: #64748b;

    font-size: 13px;

    margin-top: 7px;
}


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

    align-items: center;

    justify-content: center;

    font-weight: bold;

    font-size: 18px;
}


.admin-info strong {

    display: block;

    color: #1e293b;

    font-size: 13px;
}


.admin-info span {

    display: block;

    color: #94a3b8;

    font-size: 11px;

    margin-top: 4px;
}


/* =========================
   FORM CARD
========================= */

.card {

    background: white;

    border-radius: 18px;

    box-shadow:
        0 5px 25px
        rgba(15,23,42,.06);

    overflow: hidden;
}


.card-header {

    padding: 23px 28px;

    border-bottom:
        1px solid #edf2f0;

    display: flex;

    align-items: center;

    gap: 15px;
}


.icon {

    width: 48px;
    height: 48px;

    border-radius: 13px;

    background: #d1fae5;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 23px;
}


.card-header h2 {

    color: #123b36;

    font-size: 18px;

    margin-bottom: 5px;
}


.card-header p {

    color: #94a3b8;

    font-size: 11px;
}


/* =========================
   ERROR
========================= */

.alert {

    margin: 22px 28px 0;

    padding: 13px 15px;

    border-radius: 9px;

    background: #fee2e2;

    color: #b91c1c;

    border:
        1px solid #fecaca;

    font-size: 12px;
}


/* =========================
   FORM
========================= */

.form-container {

    padding: 28px;
}


.form-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 22px;
}


.form-group {

    display: flex;

    flex-direction: column;
}


.form-group.full {

    grid-column:
        1 / -1;
}


label {

    color: #334155;

    font-size: 12px;

    font-weight: bold;

    margin-bottom: 8px;
}


input,
select {

    width: 100%;

    height: 45px;

    border:
        1px solid #dbe3e0;

    border-radius: 9px;

    padding: 0 13px;

    background: white;

    color: #334155;

    font-size: 13px;

    outline: none;

    transition: .2s;
}


input:focus,
select:focus {

    border-color: #10b981;

    box-shadow:
        0 0 0 3px
        rgba(16,185,129,.10);
}


/* =========================
   SECTION
========================= */

.section {

    margin-top: 28px;

    padding-top: 25px;

    border-top:
        1px solid #edf2f0;
}


.section-title {

    color: #123b36;

    font-size: 15px;

    font-weight: bold;

    margin-bottom: 18px;
}


.time-grid {

    display: grid;

    grid-template-columns:
        repeat(5, 1fr);

    gap: 12px;
}


.time-box {

    background: #f8faf9;

    border:
        1px solid #e6eeeb;

    border-radius: 12px;

    padding: 15px;
}


.time-box label {

    display: block;

    font-size: 11px;

    margin-bottom: 8px;

    color: #475569;
}


.time-box input {

    height: 42px;

    padding: 0 8px;
}


/* =========================
   ACTION
========================= */

.actions {

    display: flex;

    justify-content: flex-end;

    gap: 10px;

    margin-top: 30px;

    padding-top: 23px;

    border-top:
        1px solid #edf2f0;
}


.btn {

    height: 43px;

    padding: 0 19px;

    border-radius: 9px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    text-decoration: none;

    border: none;

    cursor: pointer;

    font-size: 12px;

    font-weight: bold;

    transition: .2s;
}


.btn-back {

    background: #e2e8f0;

    color: #475569;
}


.btn-back:hover {

    background: #cbd5e1;
}


.btn-update {

    background: #059669;

    color: white;

    box-shadow:
        0 5px 15px
        rgba(5,150,105,.18);
}


.btn-update:hover {

    background: #047857;

    transform: translateY(-1px);
}


/* =========================
   FOOTER
========================= */

.footer {

    text-align: center;

    color: #94a3b8;

    font-size: 11px;

    margin-top: 25px;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:900px) {

    .time-grid {

        grid-template-columns:
            repeat(2, 1fr);
    }
}


@media(max-width:750px) {

    .sidebar {

        width: 70px;

        padding: 20px 8px;
    }

    .logo-text,
    .menu-title,
    .menu span {

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

    .admin-box {

        display: none;
    }

    .form-grid {

        grid-template-columns: 1fr;
    }

    .form-group.full {

        grid-column: auto;
    }
}


@media(max-width:500px) {

    .time-grid {

        grid-template-columns: 1fr;
    }

    .actions {

        flex-direction: column;
    }

    .btn {

        width: 100%;
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
        ADMIN PANEL
    </div>


    <a
        href="dashboard.php"
        class="menu"
    >
        <span>🏠</span>
        <span>Dashboard</span>
    </a>


    <a
        href="jadwal.php"
        class="menu active"
    >
        <span>🕐</span>
        <span>Jadwal Sholat</span>
    </a>


    <a
        href="tambah.php"
        class="menu"
    >
        <span>➕</span>
        <span>Tambah Jadwal</span>
    </a>


    <a
        href="users.php"
        class="menu"
    >
        <span>👥</span>
        <span>Data User</span>
    </a>


    <div
        class="menu-title"
        style="margin-top:30px;"
    >
        LAPORAN
    </div>


    <a
        href="print.php"
        class="menu"
    >
        <span>🖨️</span>
        <span>Print Jadwal</span>
    </a>


    <a
        href="../logout.php"
        class="menu logout"
    >
        <span>🚪</span>
        <span>Logout</span>
    </a>


</aside>


<!-- =========================
MAIN
========================= -->

<main class="main">


    <div class="header">


        <div>

            <div class="header-small">
                JADWAL SHOLAT
            </div>

            <h1>
                Edit Jadwal
            </h1>

            <p>
                Perbarui data jadwal sholat pengguna.
            </p>

        </div>


        <div class="admin-box">


            <div class="avatar">

                <?= strtoupper(
                    substr($nama_admin, 0, 1)
                ); ?>

            </div>


            <div class="admin-info">

                <strong>
                    <?= htmlspecialchars(
                        $nama_admin
                    ); ?>
                </strong>

                <span>
                    Administrator
                </span>

            </div>


        </div>


    </div>


    <!-- =========================
    CARD
    ========================= -->

    <div class="card">


        <div class="card-header">


            <div class="icon">
                ✏️
            </div>


            <div>

                <h2>
                    Edit Jadwal Sholat
                </h2>

                <p>
                    Perbarui tanggal dan waktu sholat.
                </p>

            </div>


        </div>


        <?php if ($error != ""): ?>

            <div class="alert">

                ⚠️

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <div class="form-container">


            <form
                method="POST"
            >


                <!-- USER + TANGGAL -->

                <div class="form-grid">


                    <div class="form-group">

                        <label>
                            👤 Pilih User
                        </label>

                        <select
                            name="user_id"
                            required
                        >

                            <option value="">
                                -- Pilih User --
                            </option>


                            <?php while (
                                $u =
                                mysqli_fetch_assoc($users)
                            ): ?>

                                <option
                                    value="<?= $u['id']; ?>"
                                    <?= (
                                        $u['id']
                                        ==
                                        $data['user_id']
                                    )
                                    ? 'selected'
                                    : ''
                                    ?>
                                >

                                    <?= htmlspecialchars(
                                        $u['nama']
                                    ); ?>

                                    -
                                    <?= htmlspecialchars(
                                        $u['email']
                                    ); ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            📅 Tanggal
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            value="<?= htmlspecialchars(
                                $data['tanggal']
                            ); ?>"
                            required
                        >

                    </div>


                </div>


                <!-- WAKTU -->

                <div class="section">


                    <div class="section-title">

                        🕌 Waktu Sholat

                    </div>


                    <div class="time-grid">


                        <div class="time-box">

                            <label>
                                🕌 Subuh
                            </label>

                            <input
                                type="time"
                                name="subuh"
                                value="<?= htmlspecialchars(
                                    $data['subuh']
                                ); ?>"
                                required
                            >

                        </div>


                        <div class="time-box">

                            <label>
                                🕌 Dzuhur
                            </label>

                            <input
                                type="time"
                                name="dzuhur"
                                value="<?= htmlspecialchars(
                                    $data['dzuhur']
                                ); ?>"
                                required
                            >

                        </div>


                        <div class="time-box">

                            <label>
                                🕌 Ashar
                            </label>

                            <input
                                type="time"
                                name="ashar"
                                value="<?= htmlspecialchars(
                                    $data['ashar']
                                ); ?>"
                                required
                            >

                        </div>


                        <div class="time-box">

                            <label>
                                🕌 Maghrib
                            </label>

                            <input
                                type="time"
                                name="maghrib"
                                value="<?= htmlspecialchars(
                                    $data['maghrib']
                                ); ?>"
                                required
                            >

                        </div>


                        <div class="time-box">

                            <label>
                                🕌 Isya
                            </label>

                            <input
                                type="time"
                                name="isya"
                                value="<?= htmlspecialchars(
                                    $data['isya']
                                ); ?>"
                                required
                            >

                        </div>


                    </div>


                </div>


                <!-- BUTTON -->

                <div class="actions">


                    <a
                        href="jadwal.php"
                        class="btn btn-back"
                    >
                        ← Kembali
                    </a>


                    <button
                        type="submit"
                        name="update"
                        class="btn btn-update"
                    >
                        💾 Update Jadwal
                    </button>


                </div>


            </form>


        </div>


    </div>


    <div class="footer">

        © <?= date('Y'); ?>

        SholatKu · Aplikasi Pengingat Sholat

    </div>


</main>


</body>

</html>