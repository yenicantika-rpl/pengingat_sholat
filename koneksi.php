<?php
// Nama host database
$host = "localhost";
// Username MySQL
$user = "root";
// Password MySQL
// Pada XAMPP biasanya kosong
$password = "";
// Nama database yang digunakan
$database = "pengingat_sholat";
// ===============================
// KONEKSI KE DATABASE
// ===============================

// Membuat koneksi ke database MySQL

$koneksi = mysqli_connect(
    $host,
    $user,
    $password,
    $database
);
// ===============================
// CEK KONEKSI
// ===============================

// Mengecek apakah koneksi berhasil

if (!$koneksi) {
    // Jika gagal, tampilkan pesan error
    die("Koneksi database gagal : " . mysqli_connect_error());
}
// ===============================
// PENGATURAN KARAKTER
// ===============================

// Menggunakan UTF-8 agar mendukung
// huruf, emoji, dan karakter khusus

mysqli_set_charset($koneksi, "utf8mb4");

?>