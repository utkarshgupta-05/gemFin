<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO `user` (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $password);
        $stmt->execute();
        $new_user_id = $conn->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO `balance` (user_id, current_balance) VALUES (?, 0.00)");
        $stmt2->bind_param("i", $new_user_id);
        $stmt2->execute();
        
        $stmt3 = $conn->prepare("INSERT INTO `category` (user_id, type, description) VALUES (?, 'expense', 'General')");
        $stmt3->bind_param("i", $new_user_id);
        $stmt3->execute();

        $conn->commit();
        header("Location: index.php");
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - gemFin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <form class="auth-box" method="POST">
            <h2 style="color:var(--primary);">gemFin Register</h2>
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Create Account</button>
            <p>Already have an account? <a href="index.php">Login</a></p>
        </form>
    </div>
</body>
</html>