<?php
session_start();
require_once 'config/koneksi.php';

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    try {

        // 🔥 PDO QUERY (AMAN)
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 🔐 CEK PASSWORD (PLAIN TEXT VERSI KAMU SEKARANG)
        if ($user && $password == $user['password']) {

            $_SESSION['user'] = $user['username'];

            header("Location: index.php");
            exit;

        } else {
            $error = "Username atau Password salah!";
        }

    } catch (Exception $e) {
        $error = "Terjadi error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login Sistem Parkir</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 40px;
            width: 360px;
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 14px;
            margin: 12px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #1a252f;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<div class="card">
    <h2>LOGIN</h2>

    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>