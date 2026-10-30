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
$tipe_pesan = "";

/* =========================
   SIMPAN JADWAL
========================= */
if (isset($_POST['simpan'])) {

    $tanggal = $_POST['tanggal'] ?? '';
    $subuh = $_POST['subuh'] ?? '';
    $dzuhur = $_POST['dzuhur'] ?? '';
    $ashar = $_POST['ashar'] ?? '';
    $maghrib = $_POST['maghrib'] ?? '';
    $isya = $_POST['isya'] ?? '';

    if (
        empty($tanggal) ||
        empty($subuh) ||
        empty($dzuhur) ||
        empty($ashar) ||
        empty($maghrib) ||
        empty($isya)
    ) {
        $pesan = "Semua data jadwal harus diisi.";
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

            $tanggal = "";
            $subuh = "";
            $dzuhur = "";
            $ashar = "";
            $maghrib = "";
            $isya = "";
        } else {
            $pesan = "Jadwal gagal disimpan.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Jadwal | SholatKu</title>

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
           CARD
        ========================= */

        .card {
            background: white;

            border-radius: 18px;

            padding: 30px;

            box-shadow: 0 4px 15px rgba(0,0,0,0.05);

            max-width: 900px;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 12px;

            margin-bottom: 25px;

            padding-bottom: 18px;

            border-bottom: 1px solid #e2e8f0;
        }

        .card-title-icon {
            width: 45px;
            height: 45px;

            border-radius: 12px;

            background: #ecfdf5;
            color: #047857;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 22px;
        }

        .card-title h2 {
            color: #047857;
            font-size: 20px;
        }

        .card-title p {
            color: #64748b;
            font-size: 13px;
            margin-top: 3px;
        }

        /* =========================
           ALERT
        ========================= */

        .alert {
            padding: 13px 16px;
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
           FORM
        ========================= */

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 14px;
            font-weight: bold;
            color: #334155;

            margin-bottom: 8px;
        }

        .form-group label span {
            color: #ef4444;
        }

        .form-group input {
            width: 100%;

            padding: 13px 14px;

            border: 1px solid #cbd5e1;
            border-radius: 10px;

            font-size: 14px;

            outline: none;

            background: white;

            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #10b981;

            box-shadow:
                0 0 0 3px rgba(16,185,129,0.12);
        }

        .time-box {
            position: relative;
        }

        .time-icon {
            position: absolute;
            right: 14px;
            top: 12px;
            pointer-events: none;
        }

        /* =========================
           BUTTON
        ========================= */

        .button-area {
            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid #e2e8f0;

            display: flex;
            gap: 12px;
        }

        .btn {
            border: none;

            padding: 12px 22px;

            border-radius: 10px;

            font-size: 14px;
            font-weight: bold;

            text-decoration: none;

            cursor: pointer;

            transition: 0.3s;
        }

        .btn-simpan {
            background: #047857;
            color: white;
        }

        .btn-simpan:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .btn-reset {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-reset:hover {
            background: #cbd5e1;
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

        .info-box strong {
            display: block;
            margin-bottom: 5px;
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
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

            .form-grid {
                grid-template-columns: 1fr;
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
            <a href="tambah.php" class="menu active">
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
     MAIN CONTENT
========================= -->

<main class="main">

    <!-- HEADER -->

    <div class="header">

        <div>
            <h1>Tambah Jadwal</h1>

            <p>
                Tambahkan jadwal sholat untuk akun Anda.
            </p>
        </div>

        <div class="user-name">
            👤 <?php echo htmlspecialchars($nama); ?>
        </div>

    </div>


    <!-- CARD -->

    <div class="card">

        <div class="card-title">

            <div class="card-title-icon">
                ➕
            </div>

            <div>
                <h2>Tambah Jadwal Sholat</h2>

                <p>
                    Silakan masukkan waktu sholat dengan benar.
                </p>
            </div>

        </div>


        <?php if ($pesan != ""): ?>

            <div class="alert <?php echo $tipe_pesan; ?>">
                <?php echo htmlspecialchars($pesan); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-grid">

                <!-- TANGGAL -->

                <div class="form-group full">

                    <label>
                        Tanggal <span>*</span>
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        value="<?php echo htmlspecialchars($tanggal ?? ''); ?>"
                        required
                    >

                </div>


                <!-- SUBUH -->

                <div class="form-group">

                    <label>
                        🕌 Waktu Subuh <span>*</span>
                    </label>

                    <div class="time-box">

                        <input
                            type="time"
                            name="subuh"
                            value="<?php echo htmlspecialchars($subuh ?? ''); ?>"
                            required
                        >

                        <span class="time-icon">
                            🌙
                        </span>

                    </div>

                </div>


                <!-- DZUHUR -->

                <div class="form-group">

                    <label>
                        🕌 Waktu Dzuhur <span>*</span>
                    </label>

                    <div class="time-box">

                        <input
                            type="time"
                            name="dzuhur"
                            value="<?php echo htmlspecialchars($dzuhur ?? ''); ?>"
                            required
                        >

                        <span class="time-icon">
                            ☀️
                        </span>

                    </div>

                </div>


                <!-- ASHAR -->

                <div class="form-group">

                    <label>
                        🕌 Waktu Ashar <span>*</span>
                    </label>

                    <div class="time-box">

                        <input
                            type="time"
                            name="ashar"
                            value="<?php echo htmlspecialchars($ashar ?? ''); ?>"
                            required
                        >

                        <span class="time-icon">
                            🌤️
                        </span>

                    </div>

                </div>


                <!-- MAGHRIB -->

                <div class="form-group">

                    <label>
                        🕌 Waktu Maghrib <span>*</span>
                    </label>

                    <div class="time-box">

                        <input
                            type="time"
                            name="maghrib"
                            value="<?php echo htmlspecialchars($maghrib ?? ''); ?>"
                            required
                        >

                        <span class="time-icon">
                            🌅
                        </span>

                    </div>

                </div>


                <!-- ISYA -->

                <div class="form-group full">

                    <label>
                        🕌 Waktu Isya <span>*</span>
                    </label>

                    <div class="time-box">

                        <input
                            type="time"
                            name="isya"
                            value="<?php echo htmlspecialchars($isya ?? ''); ?>"
                            required
                        >

                        <span class="time-icon">
                            🌙
                        </span>

                    </div>

                </div>

            </div>


            <!-- BUTTON -->

            <div class="button-area">

                <button
                    type="submit"
                    name="simpan"
                    class="btn btn-simpan"
                >
                    💾 Simpan Jadwal
                </button>

                <button
                    type="reset"
                    class="btn btn-reset"
                >
                    🔄 Reset
                </button>

            </div>

        </form>



    </div>

</main>

</body>
</html>