<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "vle_english_academy";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Database connection failed"]);
  exit;
}
?>
