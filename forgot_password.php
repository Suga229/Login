<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Periksa apakah email ada di database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Buat token reset (bisa berupa string unik atau UUID)
        $token = bin2hex(random_bytes(50));
        $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Kirim token reset ke email (atau tampilkan link langsung untuk kemudahan)
        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        echo "Silakan klik tautan berikut untuk mengatur ulang password: <a href='$resetLink'>$resetLink</a>";
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Lupa Password</title>
</head>
<body>
    <form method="POST" class="form-container">
        <h2>Lupa Password</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <input type="email" name="email" placeholder="Masukkan email Anda" required>
        <button type="submit">Kirim Reset Password</button>
    </form>
</body>
</html>
