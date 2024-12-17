<?php
include 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Periksa token di database
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);

            // Update password dan hapus token
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
            $stmt->bind_param("ss", $newPassword, $token);

            if ($stmt->execute()) {
                echo "Password berhasil direset. <a href='index.php'>Login di sini</a>";
                exit;
            } else {
                $error = "Terjadi kesalahan saat mengganti password.";
            }
        }
    } else {
        $error = "Token reset tidak valid atau sudah digunakan.";
    }
} else {
    header("Location: forgot_password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Reset Password</title>
</head>
<body>
    <form method="POST" class="form-container">
        <h2>Reset Password</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <input type="password" name="password" placeholder="Password Baru" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
