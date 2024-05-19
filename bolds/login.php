<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "todo_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $mysqli->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();
    if (password_verify($password, $hashed_password)) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $id;
        header("Location: todo.html");
        exit();
    } else {
        echo "Invalid username or password.";
    }
} else {
    echo "Invalid username or password.";
}

$stmt->close();
$mysqli->close();
?>
