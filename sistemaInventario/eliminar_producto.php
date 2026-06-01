<?php

include("../includes/solo_admin.php");
include("../config/conexion.php");

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    $conn->query("
        UPDATE productos
        SET activo = 0
        WHERE id = $id
    ");

}

header("Location: inventario.php");
exit;