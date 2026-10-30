<?php

session_start();

include "koneksi.php";


$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';


$email = trim($email);


if ($email == '' || $password == '') {

    $_SESSION['error'] =
        "Email dan password wajib diisi.";

    header("Location: login.php");

    exit;
}


$query = mysqli_prepare(
    $koneksi,
    "SELECT *
     FROM users
     WHERE email = ?
     LIMIT 1"
);


mysqli_stmt_bind_param(
    $query,
    "s",
    $email
);


mysqli_stmt_execute($query);


$result = mysqli_stmt_get_result($query);


$user = mysqli_fetch_assoc($result);


if (!$user) {

    $_SESSION['error'] =
        "Email tidak ditemukan.";

    header("Location: login.php");

    exit;
}


if (!password_verify(
    $password,
    $user['password']
)) {

    $_SESSION['error'] =
        "Password salah.";

    header("Location: login.php");

    exit;
}


// =================================
// LOGIN BERHASIL
// =================================

session_regenerate_id(true);


$_SESSION['user_id'] =
    $user['id'];

$_SESSION['nama'] =
    $user['nama'];

$_SESSION['email'] =
    $user['email'];

$_SESSION['role'] =
    $user['role'];


// =================================
// REDIRECT SESUAI ROLE
// =================================

if ($user['role'] == 'admin') {

    header(
        "Location: admin/dashboard.php"
    );

} else {

    header(
        "Location: user/dashboard.php"
    );
}

exit;

?>