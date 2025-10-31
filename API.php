<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'Connect Database.php';

// Get request data
$action = $_GET['action'] ?? '';

// Route actions
switch ($action) {
    case 'register':
        registerUser($conn);
        break;

    case 'login':
        loginUser($conn);
        break;

    case 'getCourses':
        getCourses($conn);
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
        break;
}

// =============== FUNCTIONS =================

function registerUser($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $conn->real_escape_string($data['full_name']);
    $email = $conn->real_escape_string($data['email']);
    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        return;
    }

    $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$name', '$email', '$password', 'student')";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}

function loginUser($conn) {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $conn->real_escape_string($data['email']);
    $password = $data['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email' LIMIT 1");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "user" => [
                    "id" => $user['user_id'],
                    "name" => $user['full_name'],
                    "email" => $user['email'],
                    "role" => $user['role']
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
}

function getCourses($conn) {
    $result = $conn->query("SELECT course_id, title, description, level, price FROM courses ORDER BY created_at DESC");
    $courses = [];

    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    echo json_encode(["status" => "success", "courses" => $courses]);
}
?>
