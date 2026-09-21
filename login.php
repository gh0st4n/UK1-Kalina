<?php

session_start();

require_once 'classes/database.php';
require_once 'classes/auth.php';

// ===============================
// KONEKSI DATABASE
// ===============================
$database = new Database();
$db = $database->getConnection();

$auth = new Auth($db);


// ===============================
// CEK JIKA SUDAH LOGIN
// ===============================
if ($auth->isLoggedIn()) {
    header("Location: index.php");
    exit();
}


// ===============================
// GENERATE CSRF TOKEN
// ===============================
if (
    empty($_SESSION['csrf_token']) ||
    !is_string($_SESSION['csrf_token'])
) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';


// ===============================
// PROSES LOGIN
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil CSRF token dari form
    $csrf_token = $_POST['csrf_token'] ?? '';

    // ===============================
    // VALIDASI CSRF
    // ===============================
    if (
        empty($csrf_token) ||
        !is_string($csrf_token) ||
        !hash_equals($_SESSION['csrf_token'], $csrf_token)
    ) {

        // Buat token baru
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $error = "Akses ditolak! Silakan refresh halaman dan coba lagi.";

    } else {

        // ===============================
        // AMBIL USERNAME & PASSWORD
        // ===============================
        $username = trim($_POST['username'] ?? '');

        // JANGAN trim password
        // Karena spasi bisa menjadi bagian dari password
        $password = $_POST['password'] ?? '';


        // ===============================
        // VALIDASI INPUT
        // ===============================
        if ($username === '' || $password === '') {

            $error = "Username dan password harus diisi.";

        } else {

            // ===============================
            // PROSES LOGIN MELALUI AUTH
            // ===============================
            if ($auth->login($username, $password)) {

                // Regenerate session ID setelah login
                session_regenerate_id(true);

                // Buat CSRF token baru setelah login
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                // Redirect ke dashboard
                header("Location: index.php");
                exit();

            } else {

                // Pesan dibuat umum agar tidak memberitahu
                // apakah username atau password yang salah
                $error = "Username atau password salah.";

                // Token CSRF diganti setelah percobaan gagal
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="robots"
        content="noindex, nofollow"
    >

    <title>Login - Manajemen Data Siswa</title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >


    <style>

        body {
            background-color: #eef2f7;

            display: flex;
            align-items: center;
            justify-content: center;

            min-height: 100vh;

            margin: 0;
        }


        .card-login {

            width: 100%;

            max-width: 400px;

            border: none;

            border-radius: 12px;
        }


        .login-icon {

            color: #0d6efd;
        }


        .form-control {

            box-shadow: none !important;
        }


        .form-control:focus {

            border-color: #86b7fe;

            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15) !important;
        }


        .btn-login {

            padding-top: 10px;

            padding-bottom: 10px;
        }

    </style>

</head>


<body>


<div class="card card-login shadow-lg p-3">

    <div class="card-body">


        <!-- ===============================
             HEADER LOGIN
        ================================ -->

        <div class="text-center mb-4">

            <i
                class="fa-solid fa-graduation-cap fa-3x text-primary mb-2"
            ></i>

            <h4 class="fw-bold text-dark">
                Login System
            </h4>

            <p class="text-muted small mb-0">
                Manajemen Data Siswa Sekolah
            </p>

        </div>



        <!-- ===============================
             PESAN ERROR
        ================================ -->

        <?php if (!empty($error)): ?>

            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >

                <i class="fa-solid fa-circle-exclamation me-2"></i>

                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>

            </div>

        <?php endif; ?>



        <!-- ===============================
             FORM LOGIN
        ================================ -->

        <form
            method="POST"
            action=""
            autocomplete="off"
            novalidate
        >

            <!-- CSRF TOKEN -->

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"
            >



            <!-- ===============================
                 USERNAME
            ================================ -->

            <div class="mb-3">

                <label
                    for="username"
                    class="form-label fw-semibold"
                >
                    Username
                </label>


                <div class="input-group">

                    <span class="input-group-text bg-light">
                        <i class="fa-solid fa-user text-secondary"></i>
                    </span>


                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control"
                        placeholder="Masukkan username"
                        autocomplete="off"
                        value=""
                        maxlength="50"
                        required
                        autofocus
                    >

                </div>

            </div>



            <!-- ===============================
                 PASSWORD
            ================================ -->

            <div class="mb-4">

                <label
                    for="password"
                    class="form-label fw-semibold"
                >
                    Password
                </label>


                <div class="input-group">

                    <span class="input-group-text bg-light">
                        <i class="fa-solid fa-lock text-secondary"></i>
                    </span>


                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="new-password"
                        value=""
                        maxlength="255"
                        required
                    >

                </div>

            </div>



            <!-- ===============================
                 BUTTON LOGIN
            ================================ -->

            <button
                type="submit"
                class="btn btn-primary btn-login w-100 fw-bold"
            >

                <i class="fa-solid fa-right-to-bracket me-2"></i>

                Masuk

            </button>

        </form>

    </div>

</div>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
></script>



<!-- ===============================
     KOSONGKAN FIELD SAAT HALAMAN DIBUKA
================================ -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    const username = document.getElementById('username');
    const password = document.getElementById('password');

    if (username) {
        username.value = '';
    }

    if (password) {
        password.value = '';
    }

});

</script>


</body>

</html>