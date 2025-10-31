<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include "connectDatabase.php"; 

$data = json_decode(file_get_contents("php://input"), true);
$fullname = $data["fullname"] ?? null;
$email = $data["email"] ?? null;
$passwordPlain = $data["password"] ?? null;
if (!$fullname || !$email || !$passwordPlain) {
  echo json_encode(["status" => "error", "message" => "Missing required fields"]);
  exit;
}
$password = password_hash($passwordPlain, PASSWORD_BCRYPT);
if (!$fullname || !$email || !$password) {
  echo json_encode(["status" => "error", "message" => "Missing required fields"]);
  exit;
}

$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'student')");
$stmt->bind_param("sss", $fullname, $email, $password);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Registration successful!"]);
} else {
  if ($conn->errno === 1062) { // MySQL duplicate entry code
    echo json_encode(["status" => "error", "message" => "Email already exists."]);
  } else {
    echo json_encode(["status" => "error", "message" => "Server error: " . $conn->error]);
  }
}

$stmt->close();
$conn->close();
?>
