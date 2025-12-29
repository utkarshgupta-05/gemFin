<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $sql = "SELECT user_id, password, first_name FROM `user` WHERE email = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {

        die("SQL Error: " . $conn->error); 
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['first_name'] = $row['first_name'];
            echo "Login Successful! Redirecting...";
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Password Incorrect. (Hash mismatch)";
        }
    } else {
        $error = "❌ No user found with email: " . htmlspecialchars($email);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Debug Mode</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <form class="auth-box" method="POST">
            <h2 style="color:var(--primary);">gemFin Login</h2>
            
            <?php if(!empty($error)): ?>
                <div style="background:#ffdddd; color:red; padding:10px; border-radius:5px; margin-bottom:10px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
            <p>New here? <a href="register.php">Register</a></p>
        </form>
    </div>
</body>
</html>