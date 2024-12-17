<?php
session_start(); // Mulai sesi
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Periksa apakah email atau username sudah digunakan
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Username atau Email sudah digunakan!";
    } else {
        // Simpan data ke database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            // Ambil ID pengguna yang baru saja didaftarkan
            $user_id = $conn->insert_id;

            // Buat sesi login
            $_SESSION['user_id'] = $user_id;

            // Redirect ke halaman home
            header("Location: home.php");
            exit;
        } else {
            $error = "Terjadi kesalahan: " . $stmt->error;
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
</head>
<body>
    <form method="POST" class="form-container">
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
        <p>Sudah punya akun? <a href="index.php">Login di sini</a></p> <!-- Tambahkan tautan kembali ke login -->
    </form>
</body>
</html>
