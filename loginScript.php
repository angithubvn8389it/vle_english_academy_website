<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

include "connectDatabase.php";

// Handle preflight OPTIONS request (for CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

// Read and decode JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Prevent warnings
$email = $data["email"] ?? null;
$password = $data["password"] ?? null;

if (!$email || !$password) {
  echo json_encode(["status" => "error", "message" => "Missing email or password"]);
  exit;
}

$stmt = $conn->prepare("SELECT user_id, full_name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo json_encode(["status" => "error", "message" => "Account not found"]);
  exit;
}

$user = $result->fetch_assoc();

// Verify password (hashed)
if (password_verify($password, $user["password"])) {
  echo json_encode([
    "status" => "success",
    "message" => "Login successful!",
    "user" => [
      "user_id" => $user["user_id"],
      "full_name" => $user["full_name"],
      "email" => $user["email"],
      "role" => $user["role"]
    ]
  ]);
} else {
  echo json_encode(["status" => "error", "message" => "Incorrect password"]);
}

$stmt->close();
$conn->close();
?>
