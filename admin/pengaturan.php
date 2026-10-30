<?php
session_start();
include "../koneksi.php";

/* =========================
   CEK LOGIN ADMIN
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

$pesan = "";
$error = "";


/* =========================
   AMBIL DATA ADMIN
========================= */

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE id = $user_id LIMIT 1"
);

$admin = mysqli_fetch_assoc($query);

if (!$admin) {
    die("Data admin tidak ditemukan.");
}


/* =========================
   SIMPAN PERUBAHAN
========================= */

if (isset($_POST['simpan'])) {

    $nama  = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $email = mysqli_real_escape_string(
        $koneksi,
        $_POST['email']
    );

    if ($nama == "" || $email == "") {

        $error = "Nama dan email tidak boleh kosong.";

    } else {

        $update = mysqli_query(
            $koneksi,
            "UPDATE users 
             SET nama='$nama', email='$email'
             WHERE id=$user_id"
        );

        if ($update) {

            $_SESSION['nama'] = $nama;

            $pesan = "Pengaturan akun berhasil diperbarui.";

            $admin['nama'] = $nama;
            $admin['email'] = $email;

        } else {

            $error = "Gagal memperbarui data akun.";
        }
    }
}


/* =========================
   GANTI PASSWORD
========================= */

if (isset($_POST['ubah_password'])) {

    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi    = $_POST['konfirmasi'];

    $password_db = $admin['password'];

    $password_valid = false;

    /* Password hash */
    if (
        password_get_info($password_db)['algo'] !== 0
    ) {

        if (password_verify($password_lama, $password_db)) {
            $password_valid = true;
        }

    } else {

        /* Password lama masih plain text */
        if ($password_lama === $password_db) {
            $password_valid = true;
        }
    }


    if (!$password_valid) {

        $error = "Password lama tidak sesuai.";

    } elseif (strlen($password_baru) < 6) {

        $error = "Password baru minimal 6 karakter.";

    } elseif ($password_baru !== $konfirmasi) {

        $error = "Konfirmasi password tidak sama.";

    } else {

        $password_hash = password_hash(
            $password_baru,
            PASSWORD_DEFAULT
        );

        $update_password = mysqli_query(
            $koneksi,
            "UPDATE users
             SET password='$password_hash'
             WHERE id=$user_id"
        );

        if ($update_password) {

            $pesan = "Password berhasil diubah.";

            $admin['password'] = $password_hash;

        } else {

            $error = "Gagal mengubah password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Pengaturan Admin | SholatKu</title>


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
    background: #f1f5f9;
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

    width: 250px;

    background: linear-gradient(
        180deg,
        #064e3b,
        #047857
    );

    color: white;

    padding: 25px 18px;

    box-shadow: 5px 0 20px rgba(0,0,0,.08);

    overflow-y: auto;
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

    border-radius: 14px;

    background: #047857;

    color: white;

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

    line-height: 1.2;
}


/* =========================
   MENU TITLE
========================= */

.menu-title {
    color: #7dd3c7;

    font-size: 10px;

    font-weight: bold;

    letter-spacing: 1px;

    padding: 0 12px;

    margin-bottom: 10px;
}


/* =========================
   MENU
========================= */

.menu {
    display: flex;

    align-items: center;

    gap: 12px;

    width: 100%;

    color: #d1fae5;

    text-decoration: none;

    padding: 13px 15px;

    margin-bottom: 6px;

    border-radius: 12px;

    font-size: 14px;

    font-weight: 500;

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


/* =========================
   LOGOUT
========================= */

.menu.logout {
    margin-top: 25px;

    color: #fecaca;
}

.menu.logout:hover {
    background: rgba(239,68,68,.15);

    color: #fecaca;

    transform: translateX(4px);
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

    padding: 25px 30px;

    margin-bottom: 25px;

    box-shadow: 0 5px 20px rgba(0,0,0,.05);
}

.header h1 {
    color: #047857;

    font-size: 26px;

    margin-bottom: 6px;
}

.header p {
    color: #64748b;

    font-size: 14px;
}


/* =========================
   CARD
========================= */

.card {
    background: white;

    border-radius: 18px;

    padding: 30px;

    margin-bottom: 25px;

    box-shadow: 0 5px 20px rgba(0,0,0,.05);
}

.card h2 {
    color: #047857;

    font-size: 20px;

    margin-bottom: 8px;
}

.card-description {
    color: #64748b;

    font-size: 14px;

    margin-bottom: 25px;
}


/* =========================
   FORM
========================= */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    color: #374151;

    font-size: 14px;

    font-weight: bold;
}

.form-group input {
    width: 100%;

    padding: 13px 14px;

    border: 1px solid #d1d5db;

    border-radius: 10px;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 14px;

    outline: none;

    transition: .2s;
}

.form-group input:focus {
    border-color: #047857;

    box-shadow:
        0 0 0 3px rgba(4,120,87,.1);
}


/* =========================
   BUTTON
========================= */

.btn {
    display: inline-block;

    padding: 12px 20px;

    border: none;

    border-radius: 10px;

    background: #047857;

    color: white;

    font-family: Arial, Helvetica, sans-serif;

    font-size: 14px;

    font-weight: bold;

    text-decoration: none;

    cursor: pointer;

    transition: .3s;
}

.btn:hover {
    background: #065f46;

    transform: translateY(-1px);
}


/* =========================
   ALERT
========================= */

.alert {
    padding: 14px 16px;

    border-radius: 10px;

    margin-bottom: 20px;

    font-size: 14px;
}

.alert-success {
    background: #dcfce7;

    color: #166534;

    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fee2e2;

    color: #991b1b;

    border: 1px solid #fecaca;
}


/* =========================
   FOOTER
========================= */

.footer {
    text-align: center;

    color: #94a3b8;

    font-size: 12px;

    margin-top: 30px;

    padding: 20px;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 768px) {

    .sidebar {
        width: 220px;
    }

    .main {
        margin-left: 220px;

        padding: 20px;
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

            <h2>SholatKu</h2>

            <p>Pengingat Sholat</p>

        </div>

    </div>


    <div class="menu-title">
        MENU UTAMA
    </div>


    <a href="dashboard.php" class="menu">

        <span class="menu-icon">
            🏠
        </span>

        Dashboard

    </a>


    <a href="jadwal.php" class="menu">
    <span class="menu-icon">🕌</span>
    <span>Jadwal Sholat</span>
</a>


    <a href="tambah.php" class="menu">
    <span class="menu-icon">➕</span>
    <span>Tambah Jadwal</span>
</a>
    <a href="users.php" class="menu">

        <span class="menu-icon">
            👥
        </span>

        Data user

    </a>


    <div class="menu-title">
        AKUN
    </div>


    <a href="profil.php" class="menu">

        <span class="menu-icon">
            👤
        </span>

        Profil Admin

    </a>


    <a href="pengaturan.php" class="menu active">

        <span class="menu-icon">
            ⚙️
        </span>

        Pengaturan

    </a>


    <div class="menu-title">
        LAPORAN
    </div>


    <a href="print.php" class="menu">

        <span class="menu-icon">
            🖨️
        </span>

        Print Jadwal

    </a>


    <a href="../logout.php" class="menu logout">

        <span class="menu-icon">
            🚪
        </span>

        Logout

    </a>

</aside>



<!-- =========================
     MAIN
========================= -->

<main class="main">


    <div class="header">

        <h1>
            Pengaturan Akun
        </h1>

        <p>
            Kelola informasi akun administrator
        </p>

    </div>



    <?php if ($pesan != ""): ?>

        <div class="alert alert-success">
            <?= htmlspecialchars($pesan); ?>
        </div>

    <?php endif; ?>


    <?php if ($error != ""): ?>

        <div class="alert alert-error">
            <?= htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>



    <!-- =========================
         DATA AKUN
    ========================= -->

    <div class="card">

        <h2>
            Informasi Akun
        </h2>

        <p class="card-description">
            Ubah nama dan email akun administrator.
        </p>


        <form method="POST">


            <div class="form-group">

                <label>
                    Nama Admin
                </label>

                <input
                    type="text"
                    name="nama"
                    value="<?= htmlspecialchars($admin['nama']); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Email Admin
                </label>

                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($admin['email']); ?>"
                    required
                >

            </div>


            <button
                type="submit"
                name="simpan"
                class="btn"
            >
                💾 Simpan Perubahan
            </button>

        </form>

    </div>



    <!-- =========================
         PASSWORD
    ========================= -->

    <div class="card">

        <h2>
            Keamanan Akun
        </h2>

        <p class="card-description">
            Ganti password administrator untuk menjaga keamanan akun.
        </p>


        <form method="POST">


            <div class="form-group">

                <label>
                    Password Lama
                </label>

                <input
                    type="password"
                    name="password_lama"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password_baru"
                    minlength="6"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    name="konfirmasi"
                    minlength="6"
                    required
                >

            </div>


            <button
                type="submit"
                name="ubah_password"
                class="btn"
            >
                🔒 Ubah Password
            </button>

        </form>

    </div>



    <div class="footer">

        © 2026 SholatKu — Pengingat Sholat

    </div>


</main>


</body>
</html>