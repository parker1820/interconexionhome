<?php

include("../includes/solo_admin.php");
include("../config/conexion.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $nombre = $_POST['nombre'];
    $categoria_id = $_POST['categoria_id'];
    $unidad = $_POST['unidad'];
    $stock = $_POST['stock'];
    $stock_minimo = $_POST['stock_minimo'];

    $stmt = $conn->prepare("
        INSERT INTO productos 
        (nombre, categoria_id, unidad, stock, stock_minimo, activo)
        VALUES (?, ?, ?, ?, ?, 1)
    ");

    $stmt->bind_param(
        "sisii",
        $nombre,
        $categoria_id,
        $unidad,
        $stock,
        $stock_minimo
    );

    $stmt->execute();

    header("Location: inventario.php");
    exit;
}