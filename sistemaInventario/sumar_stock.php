<?php

include("../includes/solo_admin.php");
include("../config/conex.php");

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    $conn->query("
        UPDATE productos
        SET stock = stock + 1
        WHERE id = $id
    ");

}

header("Location: inventario.php");
exit;