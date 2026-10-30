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
   TOTAL JADWAL
========================= */
$q_total = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total 
     FROM jadwal_sholat 
     WHERE user_id = $user_id"
);

$data_total = mysqli_fetch_assoc($q_total);
$total = $data_total['total'] ?? 0;

/* =========================
   JADWAL HARI INI
========================= */
$tanggal_hari_ini = date('Y-m-d');

$q_hari_ini = mysqli_query(
    $koneksi,
    "SELECT COUNT(*) AS total 
     FROM jadwal_sholat 
     WHERE user_id = $user_id 
     AND tanggal = '$tanggal_hari_ini'"
);

$data_hari_ini = mysqli_fetch_assoc($q_hari_ini);
$hari_ini = $data_hari_ini['total'] ?? 0;

/* =========================
   JADWAL TERBARU
========================= */
$q_jadwal = mysqli_query(
    $koneksi,
    "SELECT *
     FROM jadwal_sholat
     WHERE user_id = $user_id
     ORDER BY tanggal DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard User | SholatKu</title>

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
            color: #1e293b;
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

            border: 1px solid #e2e8f0;

            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 20px;
        }

        .header h1 {
            color: #047857;

            font-size: 26px;

            margin-bottom: 7px;
        }

        .header p {
            color: #64748b;

            font-size: 14px;
        }

        .header p b {
            color: #334155;
        }

        .date-box {
            background: #ecfdf5;

            color: #047857;

            padding: 10px 15px;

            border-radius: 10px;

            font-size: 13px;

            white-space: nowrap;
        }

        /* =========================
           STAT CARD
        ========================= */
        .card-container {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 25px;
        }

        .card {
            background: white;

            border-radius: 18px;

            padding: 23px;

            border: 1px solid #e2e8f0;

            min-height: 130px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            transition: transform 0.2s ease,
                        box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);

            box-shadow: 0 8px 25px rgba(15,23,42,0.07);
        }

        .card-label {
            color: #64748b;

            font-size: 14px;

            margin-bottom: 10px;
        }

        .card-number {
            color: #047857;

            font-size: 30px;

            font-weight: 700;

            line-height: 1;
        }

        .card-status {
            display: inline-flex;

            align-items: center;

            width: fit-content;

            background: #dcfce7;

            color: #15803d;

            padding: 7px 12px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 600;
        }

        .status-dot {
            width: 7px;
            height: 7px;

            background: #22c55e;

            border-radius: 50%;

            margin-right: 7px;
        }

        /* =========================
           CONTENT
        ========================= */
        .content-box {
            background: white;

            border-radius: 18px;

            border: 1px solid #e2e8f0;

            overflow: hidden;
        }

        .content-header {
            padding: 22px 24px;

            border-bottom: 1px solid #e2e8f0;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-header h2 {
            color: #0f172a;

            font-size: 19px;
        }

        .content-header span {
            color: #64748b;

            font-size: 13px;
        }

        /* =========================
           TABLE
        ========================= */
        .table-wrapper {
            width: 100%;

            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 700px;
        }

        th {
            background: #ecfdf5;

            color: #047857;

            padding: 14px 16px;

            text-align: left;

            font-size: 13px;

            font-weight: 600;
        }

        td {
            padding: 15px 16px;

            border-bottom: 1px solid #e2e8f0;

            color: #475569;

            font-size: 13px;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .date {
            color: #0f172a;

            font-weight: 600;
        }

        .time {
            font-weight: 500;

            color: #334155;
        }

        /* =========================
           EMPTY DATA
        ========================= */
        .empty {
            padding: 50px 25px;

            text-align: center;

            color: #64748b;
        }

        .empty-title {
            font-size: 16px;

            color: #334155;

            font-weight: 600;

            margin-bottom: 6px;
        }

        .empty-text {
            font-size: 13px;
        }

        /* =========================
           FOOTER
        ========================= */
        .footer {
            text-align: center;

            color: #94a3b8;

            font-size: 12px;

            margin-top: 25px;

            padding-bottom: 10px;
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 900px) {

            .sidebar {
                width: 220px;
            }

            .main {
                margin-left: 220px;
                padding: 20px;
            }

            .card-container {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 700px) {

            .sidebar {
                position: relative;

                width: 100%;
                height: auto;

                padding: 18px;
            }

            .logo {
                margin-bottom: 20px;
            }

            .main {
                margin-left: 0;

                padding: 15px;
            }

            .header {
                flex-direction: column;

                align-items: flex-start;
            }

            .date-box {
                width: 100%;
            }

            .card-container {
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
            <a href="dashboard.php" class="menu active">
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
            <h1>Dashboard User</h1>

            <p>
                Selamat datang,
                <b><?= htmlspecialchars($nama); ?></b> 👋
            </p>
        </div>

        <div class="date-box">
            <?= date('d F Y'); ?>
        </div>

    </div>


    <!-- STAT CARD -->
    <div class="card-container">

        <div class="card">

            <div class="card-label">
                Total Jadwal Sholat
            </div>

            <div class="card-number">
                <?= $total; ?>
            </div>

        </div>


        <div class="card">

            <div class="card-label">
                Jadwal Hari Ini
            </div>

            <div class="card-number">
                <?= $hari_ini; ?>
            </div>

        </div>


        <div class="card">

            <div class="card-label">
                Status Akun
            </div>

            <div class="card-status">

                <span class="status-dot"></span>

                Aktif

            </div>

        </div>

    </div>


    <!-- JADWAL TERBARU -->
    <div class="content-box">

        <div class="content-header">

            <h2>Jadwal Sholat Terbaru</h2>

            <span>
                5 data terakhir
            </span>

        </div>


        <?php if (mysqli_num_rows($q_jadwal) > 0): ?>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>Tanggal</th>

                            <th>Subuh</th>

                            <th>Dzuhur</th>

                            <th>Ashar</th>

                            <th>Maghrib</th>

                            <th>Isya</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($row = mysqli_fetch_assoc($q_jadwal)): ?>

                            <tr>

                                <td class="date">
                                    <?= date('d-m-Y', strtotime($row['tanggal'])); ?>
                                </td>

                                <td class="time">
                                    <?= htmlspecialchars($row['subuh']); ?>
                                </td>

                                <td class="time">
                                    <?= htmlspecialchars($row['dzuhur']); ?>
                                </td>

                                <td class="time">
                                    <?= htmlspecialchars($row['ashar']); ?>
                                </td>

                                <td class="time">
                                    <?= htmlspecialchars($row['maghrib']); ?>
                                </td>

                                <td class="time">
                                    <?= htmlspecialchars($row['isya']); ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty">

                <div class="empty-title">
                    Belum ada jadwal sholat
                </div>

                <div class="empty-text">
                    Jadwal sholat yang kamu tambahkan akan muncul di sini.
                </div>

            </div>

        <?php endif; ?>

    </div>


    <!-- FOOTER -->
    <div class="footer">

        © <?= date('Y'); ?> SholatKu — Pengingat Sholat

    </div>

</main>

</body>
</html>