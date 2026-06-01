<?php

include("session.php");

if($_SESSION['rol'] != "ADMIN"){
    header("Location: material.php");
    exit;
}

?>