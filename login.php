<?php

session_start();

if (isset($_SESSION['user_id'])) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }

    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Login | SholatKu</title>

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


.login-box {

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

.btn-login {

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


.btn-login:hover {

    background: #065f46;

    transform: translateY(-2px);
}


/* DAFTAR */

.register-link {

    text-align: center;

    margin-top: 20px;

    color: #64748b;

    font-size: 12px;
}


.register-link a {

    color: #047857;

    text-decoration: none;

    font-weight: bold;
}


.register-link a:hover {

    text-decoration: underline;
}

</style>

</head>


<body>


<div class="login-box">


    <div class="logo">

        <div class="logo-icon">
            🕌
        </div>

        <h1>
            SholatKu
        </h1>

        <p>
            Silakan login untuk melanjutkan
        </p>

    </div>


    <?php

    if (isset($_SESSION['error'])) {

        echo '<div class="alert">'
            . htmlspecialchars($_SESSION['error']) .
            '</div>';

        unset($_SESSION['error']);

    }

    ?>


    <form
        action="proses_login.php"
        method="POST"
    >


        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                placeholder="Masukkan email"
                required
            >

        </div>


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


        <button
            type="submit"
            class="btn-login"
        >
            🔐 Login
        </button>


    </form>


    <div class="register-link">

        Belum punya akun?

        <a href="daftar.php">
            Daftar di sini
        </a>

    </div>


</div>


</body>

</html>