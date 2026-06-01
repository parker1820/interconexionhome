<?php
include("session.php");
include("../config/conex.php");
/*
====================================
VALIDAR ID
====================================
*/

if(!isset($_GET['id'])){

    header("Location:ips_instalador.php");
    exit;

}

$id = intval($_GET['id']);

/*
====================================
ELIMINAR
====================================
*/

$sql = "DELETE FROM clientes
        WHERE id='$id'";

if($conn->query($sql)){

    header("Location:ips_instalador.php");

}
else{

    echo "

    <script>

        alert('Error al eliminar');

        window.location='ips_instalador.php';

    </script>

    ";

}

?>