<?php
include("session.php");
include("../config/conex.php");

/*
====================================
RECIBIR DATOS
====================================
*/

$id     = $_POST['id'];
$nombre = strtoupper(trim($_POST['nombre']));
$ip     = trim($_POST['ip']);
$red    = trim($_POST['red']);
$modo   = trim($_POST['modo']);

/*
====================================
VALIDAR IP DUPLICADA
MISMA RED
EXCEPTO EL MISMO REGISTRO
====================================
*/

$verificar = $conn->query("

    SELECT *
    FROM clientes
    WHERE (
        ip='$ip'
        OR nombre='$nombre'
    )
    AND red='$red'
    AND id != '$id'

");

/*
====================================
SI EXISTE
====================================
*/

if($verificar->num_rows > 0){

  ?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Registro Duplicado</title>

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
    width:350px;
    box-shadow:0 0 20px rgba(0,0,0,0.4);
}

.icon{
    font-size:60px;
    color:#ef4444;
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
    transition:0.3s;
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
        Registro duplicado
    </h2>

    <p>

        La IP 
        <b><?php echo $ip; ?></b>

        o el cliente

        <b><?php echo $nombre; ?></b>

        ya existen dentro de esta red.

    </p>

    <a href="javascript:history.back()"
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
    ACTUALIZAR
    ====================================
    */

    $sql = "UPDATE clientes
            SET nombre='$nombre',
                ip='$ip',
                red='$red',
                modo='$modo'
            WHERE id='$id'";

    if($conn->query($sql)){

        header("Location:../IPS/ips_instalador.php");

    }
    else{

        echo "Error al actualizar";

    }

}

?>