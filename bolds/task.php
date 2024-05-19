<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    die();
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "todo_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Set response content type to JSON
header('Content-Type: application/json');

// Check the action requested
if ($_SERVER['REQUEST_METHOD'] === 'GET') { if (isset($_GET['action']) && $_GET['action'] === 'getUsername') {
    echo json_encode(['username' => $_SESSION['username']]);}
    // Fetch tasks
   elseif (isset($_GET['action']) && $_GET['action'] === 'fetch') {
        $user_id = $_SESSION['user_id'];
        $stmt = $mysqli->prepare("SELECT id, task, completed FROM tasks WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($tasks);
    } else {
        // Invalid request
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add task
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $user_id = $_SESSION['user_id'];
        $task = $_POST['task'];
        $stmt = $mysqli->prepare("INSERT INTO tasks (user_id, task, completed) VALUES (?, ?, 0)");
        $stmt->bind_param("is", $user_id, $task);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Task could not be added']);
        }
    }
    // Update task completion
    elseif (isset($_POST['action']) && $_POST['action'] === 'complete') {
        $user_id = $_SESSION['user_id'];
        $task_id = $_POST['id'];
        $stmt = $mysqli->prepare("UPDATE tasks SET completed = NOT completed WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Task completion status could not be updated']);
        }
    }



    // Edit task
    elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $user_id = $_SESSION['user_id'];
        $task_id = $_POST['id'];
        $new_task = $_POST['task'];
        $stmt = $mysqli->prepare("UPDATE tasks SET task = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_task, $task_id, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Task could not be edited']);
        }
    }
    // Delete task
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $user_id = $_SESSION['user_id'];
        $task_id = $_POST['id'];
        $stmt = $mysqli->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Task could not be deleted']);
        }
    } else {
        // Invalid request
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
    }
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}

// Close database connection
$mysqli->close();
?>