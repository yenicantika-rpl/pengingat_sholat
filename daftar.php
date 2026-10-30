<?php

include "koneksi.php";

$pesan = "";
$tipe = "";

if (isset($_POST['daftar'])) {

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if (
        $nama == "" ||
        $email == "" ||
        $password == "" ||
        $konfirmasi == ""
    ) {

        $pesan = "Semua data wajib diisi.";
        $tipe = "error";

    } elseif ($password !== $konfirmasi) {

        $pesan = "Konfirmasi password tidak sama.";
        $tipe = "error";

    } else {

        // CEK EMAIL
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $cek,
            "s",
            $email
        );

        mysqli_stmt_execute($cek);

        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) > 0) {

            $pesan = "Email sudah terdaftar. Silakan gunakan email lain.";
            $tipe = "error";

        } else {

            // HASH PASSWORD
            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // USER BIASA
            $role = "user";

            // SIMPAN KE DATABASE
            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO users
                (nama, email, password, role)
                VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $nama,
                $email,
                $password_hash,
                $role
            );

            if (mysqli_stmt_execute($stmt)) {

                // Setelah berhasil langsung ke login
                header("Location: login.php?daftar=berhasil");
                exit;

            } else {

                $pesan =
                    "Pendaftaran gagal: " .
                    mysqli_error($koneksi);

                $tipe = "error";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Daftar | SholatKu</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {

    font-family: Arial, Helvetica, sans-serif;

    min-height: 100vh;

    display: flex;

    justify-content: center;

    align-items: center;

    background:
        linear-gradient(
            135deg,
            #064e3b,
            #047857
        );

    padding: 20px;
}


/* BOX */

.register-box {

    width: 100%;

    max-width: 430px;

    background: white;

    padding: 35px;

    border-radius: 20px;

    box-shadow:
        0 15px 40px
        rgba(0,0,0,.20);
}


/* LOGO */

.logo {

    text-align: center;

    margin-bottom: 25px;
}


.logo-icon {

    width: 65px;

    height: 65px;

    margin: auto;

    display: flex;

    justify-content: center;

    align-items: center;

    background: #047857;

    border-radius: 18px;

    font-size: 32px;

    margin-bottom: 12px;
}


.logo h1 {

    color: #064e3b;

    font-size: 25px;

    margin-bottom: 5px;
}


.logo p {

    color: #64748b;

    font-size: 12px;
}


/* ALERT */

.alert {

    padding: 12px 14px;

    border-radius: 10px;

    margin-bottom: 18px;

    font-size: 12px;
}


.alert.error {

    background: #fee2e2;

    color: #991b1b;

    border: 1px solid #fecaca;
}


/* FORM */

.form-group {

    margin-bottom: 18px;
}


.form-group label {

    display: block;

    color: #334155;

    font-size: 13px;

    font-weight: bold;

    margin-bottom: 7px;
}


.form-group input {

    width: 100%;

    padding: 13px 14px;

    border: 1px solid #dbe3e0;

    border-radius: 10px;

    font-size: 13px;

    outline: none;

    transition: .25s;
}


.form-group input:focus {

    border-color: #10b981;

    box-shadow:
        0 0 0 3px
        rgba(16,185,129,.12);
}


/* BUTTON */

.btn-daftar {

    width: 100%;

    border: none;

    background: #047857;

    color: white;

    padding: 13px;

    border-radius: 10px;

    font-size: 14px;

    font-weight: bold;

    cursor: pointer;

    transition: .25s;
}


.btn-daftar:hover {

    background: #065f46;

    transform: translateY(-2px);
}


/* LOGIN */

.login-link {

    text-align: center;

    margin-top: 20px;

    color: #64748b;

    font-size: 12px;
}


.login-link a {

    color: #047857;

    text-decoration: none;

    font-weight: bold;
}


.login-link a:hover {

    text-decoration: underline;
}


/* RESPONSIVE */

@media(max-width: 480px) {

    .register-box {

        padding: 25px;

    }

}

</style>

</head>


<body>


<div class="register-box">


    <!-- LOGO -->

    <div class="logo">

        <div class="logo-icon">
            🕌
        </div>

        <h1>
            SholatKu
        </h1>

        <p>
            Buat akun baru
        </p>

    </div>


    <!-- PESAN ERROR -->

    <?php if ($pesan != ""): ?>

        <div class="alert <?= $tipe ?>">

            <?= htmlspecialchars($pesan) ?>

        </div>

    <?php endif; ?>


    <!-- FORM DAFTAR -->

    <form method="POST">


        <!-- NAMA -->

        <div class="form-group">

            <label>
                Nama Lengkap
            </label>

            <input
                type="text"
                name="nama"
                placeholder="Masukkan nama lengkap"
                value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                required
            >

        </div>


        <!-- EMAIL -->

        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                placeholder="Masukkan email"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="form-group">

            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

        </div>


        <!-- KONFIRMASI -->

        <div class="form-group">

            <label>
                Konfirmasi Password
            </label>

            <input
                type="password"
                name="konfirmasi"
                placeholder="Ulangi password"
                required
            >

        </div>


        <!-- BUTTON -->

        <button
            type="submit"
            name="daftar"
            class="btn-daftar"
        >
            Daftar Sekarang
        </button>


    </form>


    <!-- LINK LOGIN -->

    <div class="login-link">

        Sudah punya akun?

        <a href="login.php">
            Login di sini
        </a>

    </div>


</div>


</body>

</html>