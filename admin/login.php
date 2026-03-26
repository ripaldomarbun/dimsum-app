<?php
session_start();
if (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Dimsum Mentai</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">🍜</div>
            <h1>Dimsum Mentai</h1>
            <p>Login Admin Panel</p>

            <?php if (isset($_GET['error'])): ?>
            <div id="errorMessage" class="error-message show">
                Username atau password salah!
            </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Masukkan username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-toggle">
                        <input type="password" id="password" name="password" required placeholder="Masukkan password">
                        <button type="button" class="toggle-btn" onclick="togglePassword()">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <a href="../index.php" class="back-link">← Kembali ke Website</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.toggle-btn');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }
    </script>
</body>

</html>
