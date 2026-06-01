<?php
$host = "localhost";
$user = "interc14_Admin";
$pass = "Diguis1532@@";
$db   = "NOMBRE_BASE_DATOS";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>