<?php
include("session.php");
include("../config/conexion.php");

// Obtener datos y limpiar espacios

$nombre = strtoupper(trim($_POST['nombre'] ?? ''));
$ip     = trim($_POST['ip'] ?? '');
$red    = trim($_POST['red'] ?? '');
$modo   = trim($_POST['modo'] ?? '');

$error = "";
$tituloError = "Error";
$detalleError = "";

/*
====================================
VALIDAR DATOS VACÍOS
====================================
*/

if($nombre == "" || $ip == "" || $red == "" || $modo == ""){

    $tituloError = "Datos incompletos";
    $detalleError = "Debe llenar todos los campos.";

}

/*
====================================
VALIDAR FORMATO DE IP
====================================
*/

elseif(!filter_var($ip, FILTER_VALIDATE_IP)){

    $tituloError = "IP inválida";
    $detalleError = "La dirección IP <b>$ip</b> no tiene un formato válido.";

}

/*
====================================
OBTENER SEGMENTO DE LA RED
====================================
*/

else{

    $consultaRed = $conn->query("
        SELECT segmento
        FROM redes
        WHERE nombre='$red'
        LIMIT 1
    ");

    if(!$consultaRed || $consultaRed->num_rows == 0){

        $tituloError = "Red no encontrada";
        $detalleError = "La red seleccionada no existe.";

    }
    else{

        $datosRed = $consultaRed->fetch_assoc();
        $segmento = trim($datosRed['segmento']);

        if($segmento == ""){

            $tituloError = "Segmento no configurado";
            $detalleError = "La red <b>$red</b> no tiene segmento IP configurado.";

        }
        else{

            $partesIP = explode(".", $ip);

            $segmentoIP = $partesIP[0].".".$partesIP[1].".".$partesIP[2];
            $ultimoOcteto = intval($partesIP[3]);

            /*
            ====================================
            VALIDAR QUE LA IP PERTENEZCA AL SEGMENTO
            ====================================
            */

            if($segmentoIP !== $segmento){

                $tituloError = "IP fuera de segmento";
                $detalleError = "
                    La IP <b>$ip</b> no pertenece a la red <b>$red</b>.<br><br>
                    Segmento permitido:<br>
                    <b>$segmento.2</b> hasta <b>$segmento.253</b>
                ";

            }

            /*
            ====================================
            VALIDAR RANGO PERMITIDO
            ====================================
            */

            elseif($ultimoOcteto < 2 || $ultimoOcteto > 253){

                $tituloError = "IP no permitida";
                $detalleError = "
                    No puedes usar la IP <b>$ip</b>.<br><br>
                    Solo se permite desde:<br>
                    <b>$segmento.2</b> hasta <b>$segmento.253</b>
                ";

            }

        }

    }

}

/*
====================================
SI EXISTE ERROR, MOSTRAR ALERTA
====================================
*/

if($detalleError != ""){

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title><?php echo $tituloError; ?></title>

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
    width:380px;
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
    line-height:1.5;
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
        <?php echo $tituloError; ?>
    </h2>

    <p>
        <?php echo $detalleError; ?>
    </p>

    <a href="../IPS/ips_instalador.php?red=<?php echo urlencode($red); ?>"
       class="btn">

        Volver

    </a>

</div>

</body>
</html>

<?php

exit;

}

/*
====================================
VERIFICAR IP DUPLICADA
====================================
*/

$verificar = $conn->query("

    SELECT * 
    FROM clientes
    WHERE (
        ip = '$ip'
        OR nombre = '$nombre'
    )
    AND red = '$red'

");

if ($verificar->num_rows > 0) {

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>IP Ocupada</title>

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
        IP ocupada
    </h2>

    <p>

        La dirección IP 
        <b><?php echo $ip; ?></b> o el cliente <b><?php echo $nombre; ?></b>
        ya están registrados.

    </p>

    <a href="../IPS/ips_instalador.php?red=<?php echo urlencode($red); ?>"
       class="btn">

        Volver

    </a>

</div>

</body>
</html>

<?php

} else {

    /*
    ====================================
    INSERTAR DATOS
    ====================================
    */

    $sql = "INSERT INTO clientes(nombre, ip, red, modo)
            VALUES('$nombre','$ip','$red','$modo')";

    if ($conn->query($sql)) {

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Registro Exitoso</title>

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
    color:#22c55e;
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
    background:#22c55e;
    color:white;
    text-decoration:none;
    border-radius:10px;
    transition:0.3s;
}

.btn:hover{
    background:#16a34a;
}

</style>

</head>

<body>

<div class="card">

    <div class="icon">
        ✅
    </div>

    <h2>
        Registro exitoso
    </h2>

    <p>

        El cliente fue agregado correctamente.

    </p>

    <p>

        Tipo:
        <b><?php echo $modo; ?></b>

    </p>

    <a href="index.php?red=<?php echo urlencode($red); ?>"
       class="btn">

        Volver

    </a>

</div>

</body>
</html>

<?php

    } else {

        echo "Error al registrar";

    }

}

?>