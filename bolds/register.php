<?php
$mysqli = new mysqli("localhost", "root", "", "todo_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];

// Check if username already exists
$stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "Username already taken.";
    $stmt->close();
    $mysqli->close();
    exit();
}

$stmt->close();

// Insert new user
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);

if ($stmt->execute()) {
    echo "Registration successful. <a href='login.html'>Login here</a>";
} else {
    echo "Registration failed.";
}

$stmt->close();
$mysqli->close();
?>
