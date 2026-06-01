<?php

session_start();

/*
====================================
TIEMPO MAXIMO
====================================
*/

$tiempoMaximo = 3600; // 1 hora

if(isset($_SESSION['ultimo_acceso'])){

    $tiempoTranscurrido =
        time() - $_SESSION['ultimo_acceso'];

    if($tiempoTranscurrido > $tiempoMaximo){

        session_unset();
        session_destroy();

        header("Location: /SISTEMALL/login.php?expirada=1");
        exit;

    }

}

/*
====================================
ACTUALIZAR ACTIVIDAD
====================================
*/

$_SESSION['ultimo_acceso'] = time();

/*
====================================
VALIDAR LOGIN
====================================
*/

if(!isset($_SESSION['usuario'])){

    header("Location: /SISTEMALL/login.php");
    exit;

}

?>