<?php

include("includes/session.php");
include("config/conexion.php");

/*
====================================
VALIDAR ROL INSTALADOR
====================================
*/

if($_SESSION['rol'] != "INSTALADOR"){
    header("Location: inventario.php");
    exit;
}

/*
====================================
RECIBIR DATOS
====================================
*/

$producto_id = intval($_POST['producto_id'] ?? 0);
$cantidad = intval($_POST['cantidad'] ?? 0);
$usuario_id = intval($_SESSION['id_usuario']);

if($producto_id <= 0 || $cantidad <= 0){
    die("Datos inválidos.");
}

/*
====================================
BUSCAR PRODUCTO
====================================
*/

$producto = $conn->query("
    SELECT *
    FROM productos
    WHERE id = $producto_id
    LIMIT 1
");

if(!$producto || $producto->num_rows == 0){
    die("Producto no encontrado.");
}

$p = $producto->fetch_assoc();

/*
====================================
VALIDAR STOCK
====================================
*/

if($p['stock'] < $cantidad){
    die("No hay suficiente stock disponible.");
}

/*
====================================
DESCONTAR STOCK
====================================
*/

$nuevoStock = $p['stock'] - $cantidad;

$actualizar = $conn->query("
    UPDATE productos
    SET stock = $nuevoStock
    WHERE id = $producto_id
");

if(!$actualizar){
    die("Error al actualizar stock: ".$conn->error);
}

/*
====================================
REGISTRAR MOVIMIENTO
====================================
*/

$observacion = "Reportado por instalador";

$movimiento = $conn->query("
    INSERT INTO movimientos(producto_id, usuario_id, tipo, cantidad, observacion)
    VALUES($producto_id, $usuario_id, 'SALIDA', $cantidad, '$observacion')
");

if(!$movimiento){
    die("Error al registrar movimiento: ".$conn->error);
}

/*
====================================
REGRESAR
====================================
*/

header("Location: material.php");
exit;

?>