<?php

include("includes/session.php");

if($_SESSION['rol'] == "ADMIN"){

    header("Location: sistemaInventario/inventario.php");
    exit;

}else{

    header("Location: sistemaInventario/material.php");
    exit;

}

?>