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

$pesan = "";
$tipe = "";

/* =========================
   AMBIL DATA USER
========================= */

$query_user = mysqli_query(
    $koneksi,
    "SELECT * FROM users WHERE id = $user_id LIMIT 1"
);

$user = mysqli_fetch_assoc($query_user);

if (!$user) {
    echo "Data pengguna tidak ditemukan.";
    exit;
}

$nama = $user['nama'] ?? 'User';
$email = $user['email'] ?? '';

/* =========================
   UPDATE NAMA DAN EMAIL
========================= */

if (isset($_POST['simpan_profil'])) {

    $nama_baru = trim($_POST['nama'] ?? '');
    $email_baru = trim($_POST['email'] ?? '');

    if ($nama_baru == "" || $email_baru == "") {

        $pesan = "Nama dan email wajib diisi.";
        $tipe = "error";

    } elseif (!filter_var($email_baru, FILTER_VALIDATE_EMAIL)) {

        $pesan = "Format email tidak valid.";
        $tipe = "error";

    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE users SET nama = ?, email = ? WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $nama_baru,
            $email_baru,
            $user_id
        );

        if (mysqli_stmt_execute($stmt)) {

            $_SESSION['nama'] = $nama_baru;

            $nama = $nama_baru;
            $email = $email_baru;

            $pesan = "Pengaturan profil berhasil disimpan.";
            $tipe = "success";

        } else {

            $pesan = "Gagal menyimpan pengaturan.";
            $tipe = "error";
        }

        mysqli_stmt_close($stmt);
    }
}


/* =========================
   GANTI PASSWORD
========================= */

if (isset($_POST['ganti_password'])) {

    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if (
        $password_lama == "" ||
        $password_baru == "" ||
        $konfirmasi == ""
    ) {

        $pesan = "Semua kolom password wajib diisi.";
        $tipe = "error";

    } elseif ($password_baru !== $konfirmasi) {

        $pesan = "Konfirmasi password tidak sama.";
        $tipe = "error";

    } elseif (strlen($password_baru) < 6) {

        $pesan = "Password baru minimal 6 karakter.";
        $tipe = "error";

    } else {

        $password_db = $user['password'] ?? '';

        /*
         * Mendukung password biasa maupun password_hash()
         */

        $password_valid = false;

        if (password_verify($password_lama, $password_db)) {
            $password_valid = true;
        } elseif ($password_lama === $password_db) {
            $password_valid = true;
        }

        if (!$password_valid) {

            $pesan = "Password lama salah.";
            $tipe = "error";

        } else {

            /*
             * Gunakan password_hash untuk password baru
             */

            $password_hash = password_hash(
                $password_baru,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE users SET password = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $password_hash,
                $user_id
            );

            if (mysqli_stmt_execute($stmt)) {

                $pesan = "Password berhasil diubah.";
                $tipe = "success";

            } else {

                $pesan = "Password gagal diubah.";
                $tipe = "error";
            }

            mysqli_stmt_close($stmt);
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

<title>Pengaturan Akun | SholatKu</title>

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


/* LOGO */

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
/* MENU TITLE */

.menu-title {
    font-size: 11px;

    color: #a7f3d0;

    font-weight: bold;

    padding: 10px 15px;

    margin-top: 5px;

    text-transform: uppercase;
}


/* MENU */

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


/* LOGOUT */

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

    box-shadow:
        0 4px 15px rgba(0,0,0,0.05);

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
   ALERT
========================= */

.alert {
    padding: 14px 17px;

    border-radius: 10px;

    margin-bottom: 20px;

    font-size: 14px;

    font-weight: 500;
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


/* =========================
   GRID
========================= */

.settings-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

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
        0 4px 15px rgba(0,0,0,0.05);
}

.card-header {
    display: flex;

    align-items: center;

    gap: 13px;

    padding-bottom: 20px;

    margin-bottom: 22px;

    border-bottom: 1px solid #e2e8f0;
}

.card-icon {
    width: 45px;

    height: 45px;

    background: #ecfdf5;

    color: #047857;

    border-radius: 12px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 22px;
}

.card-header h2 {
    color: #047857;

    font-size: 19px;

    margin-bottom: 4px;
}

.card-header p {
    color: #64748b;

    font-size: 13px;
}


/* =========================
   FORM
========================= */

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    color: #334155;

    font-size: 14px;

    font-weight: bold;
}

.form-group input {
    width: 100%;

    padding: 13px 14px;

    border: 1px solid #cbd5e1;

    border-radius: 10px;

    font-size: 14px;

    outline: none;

    transition: 0.3s;
}

.form-group input:focus {
    border-color: #10b981;

    box-shadow:
        0 0 0 3px rgba(16,185,129,0.12);
}


/* =========================
   BUTTON
========================= */

.btn {
    width: 100%;

    border: none;

    padding: 13px;

    border-radius: 10px;

    background: #047857;

    color: white;

    font-size: 14px;

    font-weight: bold;

    cursor: pointer;

    transition: 0.3s;
}

.btn:hover {
    background: #059669;

    transform: translateY(-2px);
}


/* =========================
   INFO
========================= */

.info-box {
    margin-top: 25px;

    background: #ecfdf5;

    border-left: 4px solid #10b981;

    padding: 17px;

    border-radius: 10px;

    color: #065f46;

    font-size: 13px;

    line-height: 1.7;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

    .settings-grid {
        grid-template-columns: 1fr;
    }

}

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

    .card {
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
            <a href="profil.php" class="menu">
                <span class="menu-icon">👤</span>
                <span>Profil</span>
            </a>
        </li>

        <li>
            <a href="pengaturan.php" class="menu active">
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

            <h1>Pengaturan Akun</h1>

            <p>
                Kelola informasi dan keamanan akun Anda.
            </p>

        </div>


        <div class="user-name">

            👤 <?php echo htmlspecialchars($nama); ?>

        </div>

    </div>


    <!-- ALERT -->

    <?php if ($pesan != ""): ?>

        <div class="alert <?php echo $tipe; ?>">

            <?php echo htmlspecialchars($pesan); ?>

        </div>

    <?php endif; ?>


    <!-- SETTINGS -->

    <div class="settings-grid">


        <!-- =====================
             PROFIL
        ====================== -->

        <div class="card">

            <div class="card-header">

                <div class="card-icon">
                    👤
                </div>

                <div>

                    <h2>Informasi Profil</h2>

                    <p>
                        Ubah nama dan email akun Anda.
                    </p>

                </div>

            </div>


            <form method="POST">

                <div class="form-group">

                    <label>
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="nama"
                        value="<?php echo htmlspecialchars($nama); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="<?php echo htmlspecialchars($email); ?>"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="simpan_profil"
                    class="btn"
                >
                    💾 Simpan Perubahan
                </button>

            </form>

        </div>


        <!-- =====================
             PASSWORD
        ====================== -->

        <div class="card">

            <div class="card-header">

                <div class="card-icon">
                    🔐
                </div>

                <div>

                    <h2>Keamanan Akun</h2>

                    <p>
                        Ubah password akun Anda.
                    </p>

                </div>

            </div>


            <form method="POST">


                <div class="form-group">

                    <label>
                        Password Lama
                    </label>

                    <input
                        type="password"
                        name="password_lama"
                        placeholder="Masukkan password lama"
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
                        placeholder="Minimal 6 karakter"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Konfirmasi Password Baru
                    </label>

                    <input
                        type="password"
                        name="konfirmasi_password"
                        placeholder="Ulangi password baru"
                        required
                    >

                </div>


                <button
                    type="submit"
                    name="ganti_password"
                    class="btn"
                >
                    🔒 Ganti Password
                </button>

            </form>

        </div>

    </div>


    <!-- INFO -->

    <div class="info-box">

        <strong>💡 Tips Keamanan</strong>

        Gunakan password yang sulit ditebak dan jangan
        membagikan password akun Anda kepada orang lain.

    </div>


</main>

</body>

</html>