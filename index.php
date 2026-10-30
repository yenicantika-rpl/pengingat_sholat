<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SholatKu - Pengingat Sholat</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f4f8f6;
    color: #173b35;
}

/* =========================
   NAVBAR
========================= */

.navbar {
    height: 75px;
    background: white;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 8%;

    box-shadow: 0 3px 15px rgba(0,0,0,.06);
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
}

.logo-icon {
    width: 47px;
    height: 47px;

    background: #10b981;
    color: white;

    border-radius: 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 24px;
}

.logo h2 {
    color: #064e3b;
    font-size: 20px;
}

.logo p {
    color: #64748b;
    font-size: 10px;
    margin-top: 3px;
}

.login-btn {
    text-decoration: none;

    background: #059669;
    color: white;

    padding: 12px 23px;

    border-radius: 9px;

    font-size: 13px;
    font-weight: bold;

    transition: .2s;
}

.login-btn:hover {
    background: #047857;
    transform: translateY(-2px);
}

/* =========================
   HERO
========================= */

.hero {
    min-height: calc(100vh - 75px);

    padding: 60px 8%;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 50px;

    background:
        linear-gradient(
            135deg,
            #ecfdf5,
            #f8fafc
        );
}

.hero-text {
    max-width: 600px;
}

.badge {
    display: inline-block;

    padding: 9px 15px;

    background: #d1fae5;

    color: #047857;

    border-radius: 30px;

    font-size: 11px;
    font-weight: bold;

    margin-bottom: 20px;
}

.hero h1 {
    color: #064e3b;

    font-size: 50px;

    line-height: 1.15;

    margin-bottom: 20px;
}

.hero h1 span {
    color: #10b981;
}

.hero-description {
    color: #64748b;

    font-size: 15px;

    line-height: 1.8;

    margin-bottom: 30px;

    max-width: 540px;
}

.hero-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-login {
    display: inline-block;

    text-decoration: none;

    background: #059669;
    color: white;

    padding: 14px 25px;

    border-radius: 10px;

    font-size: 13px;
    font-weight: bold;

    box-shadow:
        0 8px 20px
        rgba(5,150,105,.20);

    transition: .2s;
}

.btn-login:hover {
    background: #047857;
    transform: translateY(-2px);
}

.btn-feature {
    display: inline-block;

    text-decoration: none;

    background: white;
    color: #047857;

    padding: 14px 25px;

    border-radius: 10px;

    font-size: 13px;
    font-weight: bold;

    border: 1px solid #d1fae5;
}

/* =========================
   ILUSTRASI
========================= */

.hero-circle {
    width: 390px;
    height: 390px;

    flex-shrink: 0;

    background:
        linear-gradient(
            135deg,
            #064e3b,
            #059669
        );

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    box-shadow:
        0 30px 60px
        rgba(6,78,59,.20);
}

.mosque {
    text-align: center;
    color: white;
}

.mosque-icon {
    font-size: 105px;
    margin-bottom: 10px;
}

.mosque h2 {
    font-size: 28px;
    margin-bottom: 8px;
}

.mosque p {
    color: #d1fae5;
    font-size: 12px;
}

/* =========================
   FITUR
========================= */

.features {
    background: white;

    padding: 70px 8%;

    text-align: center;
}

.features h2 {
    color: #064e3b;

    font-size: 30px;

    margin-bottom: 10px;
}

.features > p {
    color: #64748b;

    font-size: 13px;

    margin-bottom: 40px;
}

.feature-container {
    max-width: 1100px;

    margin: auto;

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 22px;
}

.feature {
    background: #f8fafc;

    border:
        1px solid #edf2f0;

    border-radius: 16px;

    padding: 30px 25px;

    transition: .2s;
}

.feature:hover {
    transform: translateY(-5px);

    box-shadow:
        0 15px 30px
        rgba(15,23,42,.08);
}

.feature-icon {
    width: 60px;
    height: 60px;

    margin: 0 auto 18px;

    background: #d1fae5;

    border-radius: 16px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 28px;
}

.feature h3 {
    color: #064e3b;

    font-size: 16px;

    margin-bottom: 10px;
}

.feature p {
    color: #64748b;

    font-size: 12px;

    line-height: 1.7;
}

/* =========================
   CTA
========================= */

.cta {
    background:
        linear-gradient(
            135deg,
            #063b35,
            #047857
        );

    color: white;

    text-align: center;

    padding: 65px 20px;
}

.cta h2 {
    font-size: 30px;
    margin-bottom: 12px;
}

.cta p {
    color: #d1fae5;
    font-size: 13px;
    margin-bottom: 25px;
}

.cta a {
    display: inline-block;

    text-decoration: none;

    background: white;
    color: #047857;

    padding: 13px 25px;

    border-radius: 9px;

    font-size: 13px;
    font-weight: bold;
}

/* =========================
   FOOTER
========================= */

footer {
    background: #052e2a;

    color: #a7f3d0;

    text-align: center;

    padding: 20px;

    font-size: 11px;
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

    .hero {
        flex-direction: column;

        text-align: center;

        padding-top: 50px;
    }

    .hero-description {
        margin-left: auto;
        margin-right: auto;
    }

    .hero-buttons {
        justify-content: center;
    }

    .hero-circle {
        width: 300px;
        height: 300px;
    }

    .mosque-icon {
        font-size: 80px;
    }

    .feature-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 500px) {

    .navbar {
        padding: 0 20px;
    }

    .logo p {
        display: none;
    }

    .login-btn {
        padding: 10px 15px;
    }

    .hero {
        padding-left: 20px;
        padding-right: 20px;
    }

    .hero h1 {
        font-size: 36px;
    }

    .hero-description {
        font-size: 14px;
    }

    .hero-buttons {
        flex-direction: column;
    }

    .hero-buttons a {
        width: 100%;
    }

    .hero-circle {
        width: 250px;
        height: 250px;
    }

    .mosque-icon {
        font-size: 65px;
    }

    .mosque h2 {
        font-size: 22px;
    }
}

</style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

    <div class="logo">

        <div class="logo-icon">
            🕌
        </div>

        <div>

            <h2>SholatKu</h2>

            <p>
                Aplikasi Pengingat Sholat
            </p>

        </div>

    </div>


    
</nav>


<!-- HERO -->

<section class="hero">


    <div class="hero-text">

        <div class="badge">
            🕌 APLIKASI PENGINGAT SHOLAT
        </div>


        <h1>

            Jangan Lewatkan
            <br>

            <span>
                Waktu Sholat
            </span>

        </h1>


        <p class="hero-description">

            SholatKu membantu Anda mengelola
            jadwal sholat dengan mudah dan
            teratur. Kelola jadwal, cari data,
            dan cetak laporan dalam satu aplikasi.

        </p>


        <div class="hero-buttons">

            <a
                href="login.php"
                class="btn-login"
            >
                🔐 Masuk ke Aplikasi →
            </a>


            <a
                href="#fitur"
                class="btn-feature"
            >
                ✨ Lihat Fitur
            </a>

        </div>

    </div>


    <div class="hero-circle">

        <div class="mosque">

            <div class="mosque-icon">
                🕌
            </div>

            <h2>
                SholatKu
            </h2>

            <p>
                Pengingat Sholat Anda
            </p>

        </div>

    </div>


</section>


<!-- FITUR -->

<section
    class="features"
    id="fitur"
>

    <h2>
        Fitur SholatKu
    </h2>

    <p>
        Semua kebutuhan pengelolaan jadwal
        sholat dalam satu aplikasi.
    </p>


    <div class="feature-container">


        <div class="feature">

            <div class="feature-icon">
                🕐
            </div>

            <h3>
                Jadwal Sholat
            </h3>

            <p>
                Kelola jadwal Subuh, Dzuhur,
                Ashar, Maghrib, dan Isya
                dengan mudah.
            </p>

        </div>


        <div class="feature">

            <div class="feature-icon">
                👥
            </div>

            <h3>
                Admin & User
            </h3>

            <p>
                Sistem login dengan hak akses
                admin dan user yang berbeda.
            </p>

        </div>


        <div class="feature">

            <div class="feature-icon">
                🖨️
            </div>

            <h3>
                Print Laporan
            </h3>

            <p>
                Cetak jadwal sholat menjadi
                laporan yang rapi dan mudah
                digunakan.
            </p>

        </div>


    </div>

</section>


<!-- CTA -->

<section class="cta">

    <h2>
        Siap Menggunakan SholatKu?
    </h2>

    <p>
        Masuk ke aplikasi dan mulai
        mengelola jadwal sholat.
    </p>


<!-- FOOTER -->

<footer>

    © <?= date('Y'); ?>

    SholatKu · Aplikasi Pengingat Sholat

</footer>


</body>

</html>