<?php

session_start();
include("config/conexion.php");

$usuario = trim($_POST['usuario'] ?? '');
$password = trim($_POST['password'] ?? '');

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows > 0){

    $datos = $resultado->fetch_assoc();

    if(password_verify($password, $datos['password'])){

        $_SESSION['id_usuario'] = $datos['id'];
        $_SESSION['usuario'] = $datos['usuario'];
        $_SESSION['nombre'] = $datos['nombre'];
        $_SESSION['rol'] = $datos['rol'];

        header("Location: menu.php");
        exit;
    }
}

header("Location: login.php");
exit;

?>