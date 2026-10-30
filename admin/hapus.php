<?php

session_start();

include "../koneksi.php";

// ===============================
// CEK LOGIN
// ===============================

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// ===============================
// CEK ADMIN
// ===============================

if ($_SESSION['role'] != 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

// ===============================
// CEK ID
// ===============================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: jadwal.php");
    exit;
}

$id = (int) $_GET['id'];

// ===============================
// HAPUS DATA
// ===============================

$query = mysqli_prepare(
    $koneksi,
    "DELETE FROM jadwal_sholat WHERE id = ?"
);

mysqli_stmt_bind_param(
    $query,
    "i",
    $id
);

mysqli_stmt_execute($query);

// ===============================
// KEMBALI
// ===============================

header("Location: jadwal.php");

exit;

?>