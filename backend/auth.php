<?php
session_start();
include 'db_connect.php'; // siguraduhin tama path

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['username'], $_POST['password'])) {
        echo "❌ Missing username or password.";
        exit;
    }
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Plain text password check (testing only)
        if ($password === $row['password']) {
            // Save session
            $_SESSION['user_id'] = $row['user_id']; 
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            header("Location: ../layout.php");
            exit;
        } else {
            echo "❌ Invalid password";
        }
    } else {
        echo "❌ Username not found";
    }
    $stmt->close();
}
