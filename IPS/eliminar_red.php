<?php
include("session.php");
include("../config/conex.php");

/*
====================================
OBTENER NOMBRE RED
====================================
*/

$nombre = $_GET['nombre'];

/*
====================================
OBTENER DATOS RED
====================================
*/

$redData = $conn->query("

    SELECT *
    FROM redes
    WHERE nombre='$nombre'

");

$red = $redData->fetch_assoc();

/*
====================================
VALIDAR EXISTENCIA
====================================
*/

if(!$red){

    die("Red no encontrada");

}

$red_id = $red['id'];

/*
====================================
VALIDAR APS
====================================
*/

$verificarAPS = $conn->query("

    SELECT *
    FROM aps
    WHERE red_id='$red_id'

");

/*
====================================
VALIDAR CLIENTES
====================================
*/

$verificarClientes = $conn->query("

    SELECT *
    FROM clientes
    WHERE red='$nombre'

");

/*
====================================
SI EXISTEN RELACIONES
====================================
*/

if(
    $verificarAPS->num_rows > 0
    ||
    $verificarClientes->num_rows > 0
){

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>No se puede eliminar</title>

<style>

body{
    margin:0;
    padding:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#0f172a;
    font-family:Arial, Helvetica, sans-serif;
}

.card{
    background:#1e293b;
    padding:40px;
    border-radius:15px;
    text-align:center;
    width:400px;
    box-shadow:0 0 20px rgba(0,0,0,0.4);
}

.icon{
    font-size:60px;
    color:#facc15;
    margin-bottom:15px;
}

h2{
    color:white;
    margin-bottom:10px;
}

p{
    color:#cbd5e1;
    margin-bottom:25px;
}

.btn{
    display:inline-block;
    padding:12px 25px;
    background:#3b82f6;
    color:white;
    text-decoration:none;
    border-radius:10px;
}

.btn:hover{
    background:#2563eb;
}

</style>

</head>

<body>

<div class="card">

    <div class="icon">
        ⚠️
    </div>

    <h2>
        No se puede eliminar
    </h2>

    <p>

        La red

        <b><?php echo $nombre; ?></b>

        tiene IPs o APs asociados.

    </p>

    <a href="../IPS/ips_instalador.php"
       class="btn">

        Volver

    </a>

</div>

</body>
</html>

<?php

}
else{

    /*
    ====================================
    ELIMINAR RED
    ====================================
    */

    $sql = "DELETE FROM redes
            WHERE id='$red_id'";

    if($conn->query($sql)){

        header("Location:../IPS/ips_instalador.php");

    }
    else{

        echo "Error al eliminar";

    }

}

?>